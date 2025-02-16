<?php
/*
 * OpenSTAManager: il software gestionale open source per l'assistenza tecnica e la fatturazione
 * Copyright (C) DevCode s.r.l.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

include_once __DIR__.'/../../core.php';

use Modules\Fatture\Fattura;

$date_start = $_SESSION['period_start'];
$date_end = $_SESSION['period_end'];

$module = Modules::get('Scadenzario');
$id_module = $module['id'];

$total = Util\Query::readQuery($module);

// Lettura parametri modulo
$module_query = $total['query'];

$search_filters = [];

if (is_array($_SESSION['module_'.$id_module])) {
    foreach ($_SESSION['module_'.$id_module] as $field => $value) {
        if (!empty($value) && string_starts_with($field, 'search_')) {
            $field_name = str_replace('search_', '', $field);
            $field_name = str_replace('__', ' ', $field_name);
            $field_name = str_replace('-', ' ', $field_name);
            array_push($search_filters, '`'.$field_name.'` LIKE "%'.$value.'%"');
        }
    }
}

if (!empty($search_filters)) {
    $module_query = str_replace('2=2', '2=2 AND ('.implode(' AND ', $search_filters).') ', $module_query);
}

$module_query = str_replace('1=1', '1=1 AND ABS(`co_scadenziario`.`pagato`) < ABS(`co_scadenziario`.`da_pagare`) ', $module_query);

// Scelgo la query in base alla scadenza
if (isset($id_record)) {
    $record = $dbo->fetchOne('SELECT * FROM co_scadenziario WHERE id = '.prepare($id_record));
    $documento = Fattura::find($record['iddocumento']);
    if (!empty($documento)) {
        $module_query = str_replace('1=1', '1=1 AND co_scadenziario.iddocumento='.prepare($documento->id), $module_query);
    } else {
        $module_query = str_replace('1=1', '1=1 AND co_scadenziario.id='.prepare($id_record), $module_query);
    }
}

// Filtri derivanti dai permessi (eventuali)
$module_query = Modules::replaceAdditionals($id_module, $module_query);

// Scadenze
$records = $dbo->fetchArray($module_query);
