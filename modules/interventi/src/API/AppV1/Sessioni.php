<?php

namespace Modules\Interventi\API\AppV1;

use API\AppResource;
use Auth;
use Carbon\Carbon;

class Sessioni extends AppResource
{
    protected function getCleanupData()
    {
        // Periodo per selezionare interventi
        $today = new Carbon();
        $start = $today->copy()->subMonths(2);
        $end = $today->copy()->addMonth(1);

        // Informazioni sull'utente
        $user = Auth::user();
        $id_tecnico = $user->id_anagrafica;

        $query = 'SELECT in_interventi_tecnici.id FROM in_interventi_tecnici
            INNER JOIN in_interventi ON in_interventi_tecnici.idintervento = in_interventi.id
        WHERE
            in_interventi.deleted_at IS NOT NULL
            OR (orario_fine NOT BETWEEN :period_start AND :period_end AND idtecnico = :id_tecnico)';
        $records = database()->fetchArray($query, [
            ':period_end' => $end,
            ':period_start' => $start,
            ':id_tecnico' => $id_tecnico,
        ]);

        return array_column($records, 'id');
    }

    protected function getData($last_sync_at)
    {
        // Periodo per selezionare interventi
        $today = new Carbon();
        $start = $today->copy()->subMonths(2);
        $end = $today->copy()->addMonth(1);

        // Informazioni sull'utente
        $user = Auth::user();
        $id_tecnico = $user->id_anagrafica;

        $query = 'SELECT in_interventi_tecnici.id FROM in_interventi_tecnici
            INNER JOIN in_interventi ON in_interventi_tecnici.idintervento = in_interventi.id
        WHERE
            in_interventi.deleted_at IS NULL
            AND (orario_fine BETWEEN :period_start AND :period_end AND idtecnico = :id_tecnico)';

        // Filtro per data
        if ($last_sync_at) {
            $last_sync = new Carbon($last_sync_at);
            $query .= ' AND in_interventi_tecnici.updated_at > '.prepare($last_sync);
        }
        $records = database()->fetchArray($query, [
            ':period_start' => $start,
            ':period_end' => $end,
            ':id_tecnico' => $id_tecnico,
        ]);

        return array_column($records, 'id');
    }

    protected function getDetails($id)
    {
        // Gestione della visualizzazione dei dettagli del record
        $query = 'SELECT id,
            idintervento AS id_intervento,
            orario_inizio,
            orario_fine
        FROM in_interventi_tecnici
        WHERE in_interventi_tecnici.id = '.prepare($id);

        $record = database()->fetchOne($query);

        return $record;
    }
}
