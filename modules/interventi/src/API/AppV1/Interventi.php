<?php

namespace Modules\Interventi\API\AppV1;

use API\AppResource;
use Auth;
use Carbon\Carbon;

class Interventi extends AppResource
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

        $query = 'SELECT in_interventi.id FROM in_interventi WHERE
            deleted_at IS NOT NULL
            OR EXISTS(
                SELECT orario_fine FROM in_interventi_tecnici WHERE
                    in_interventi_tecnici.idintervento = in_interventi.id
                    AND orario_fine NOT BETWEEN :period_start AND :period_end
                    AND idtecnico = :id_tecnico
                )';
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

        $query = 'SELECT in_interventi.id FROM in_interventi WHERE
            in_interventi.id IN (
                SELECT idintervento FROM in_interventi_tecnici
                WHERE in_interventi_tecnici.idintervento = in_interventi.id
                    AND in_interventi_tecnici.orario_fine BETWEEN :period_start AND :period_end
                    AND in_interventi_tecnici.idtecnico = :id_tecnico
            )
            AND deleted_at IS NULL';

        // Filtro per data
        if ($last_sync_at) {
            $last_sync = new Carbon($last_sync_at);
            $query .= ' AND in_interventi.updated_at > '.prepare($last_sync);
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
        $query = "SELECT id,
            codice,
            richiesta,
            data_richiesta,
            descrizione,
            idanagrafica AS id_anagrafica,
            idtipointervento AS id_tipo_intervento,
            idstatointervento AS id_stato_intervento,
            informazioniaggiuntive AS informazioni_aggiuntive,
            IF(idsede_destinazione = 0, NULL, idsede_destinazione) AS id_sede,
            firma_file,
            IF(firma_data = '0000-00-00 00:00:00', '', firma_data) AS firma_data,
            firma_nome
        FROM in_interventi
        WHERE in_interventi.id = ".prepare($id);

        $record = database()->fetchOne($query);

        return $record;
    }
}
