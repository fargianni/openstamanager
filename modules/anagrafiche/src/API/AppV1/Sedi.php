<?php

namespace Modules\Anagrafiche\API\AppV1;

use API\AppResource;
use Carbon\Carbon;

class Sedi extends AppResource
{
    protected function getCleanupData()
    {
        return $this->getMissingIDs('an_sedi', 'id');
    }

    protected function getData($last_sync_at)
    {
        $query = "SELECT DISTINCT(an_sedi.id) AS id FROM an_sedi
            INNER JOIN an_anagrafiche ON an_anagrafiche.idanagrafica = an_sedi.idanagrafica
            INNER JOIN an_tipianagrafiche_anagrafiche ON an_tipianagrafiche_anagrafiche.idanagrafica = an_anagrafiche.idanagrafica
            INNER JOIN an_tipianagrafiche ON an_tipianagrafiche_anagrafiche.idtipoanagrafica = an_tipianagrafiche.idtipoanagrafica
        WHERE an_tipianagrafiche.descrizione = 'Cliente' AND an_anagrafiche.deleted_at IS NULL";

        // Filtro per data
        if ($last_sync_at) {
            $last_sync = new Carbon($last_sync_at);
            $query .= ' AND an_sedi.updated_at > '.prepare($last_sync);
        }

        $records = database()->fetchArray($query);

        return array_column($records, 'id');
    }

    protected function getDetails($id)
    {
        // Gestione della visualizzazione dei dettagli del record
        $query = 'SELECT an_sedi.id,
            an_sedi.idanagrafica AS id_anagrafica,
            an_sedi.nomesede AS nome,
            an_sedi.piva AS partita_iva,
            an_sedi.codice_fiscale,
            an_sedi.indirizzo,
            an_sedi.indirizzo2,
            an_sedi.citta,
            an_sedi.cap,
            an_sedi.provincia,
            an_sedi.km,
            IFNULL(an_sedi.lat, 0.00) AS latitudine,
            IFNULL(an_sedi.lng, 0.00) AS longitudine,
            an_nazioni.nome AS nazione,
            an_sedi.telefono,
            an_sedi.cellulare,
            an_sedi.fax,
            an_sedi.email
        FROM an_sedi
            LEFT OUTER JOIN an_nazioni ON an_sedi.id_nazione = an_nazioni.id
        WHERE an_sedi.id = '.prepare($id);

        $record = database()->fetchOne($query);

        return $record;
    }
}
