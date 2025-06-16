<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncTPGStops extends Command
{
    protected $signature = 'stops:sync';
    protected $description = 'Synchronise tous les arrêts TPG avec la base de données';

    public function handle()
    {
        $this->info('Synchronisation des arrêts TPG...');

        $url = 'https://opendata.tpg.ch/api/explore/v2.1/catalog/datasets/arrets/exports/json?lang=fr&timezone=Europe%2FBerlin';

        try {
            $response = Http::timeout(60)->get($url);

            if ($response->failed()) {
                $this->error("Erreur lors du téléchargement du fichier JSON.");
                return 1;
            }

            $stops = $response->json();

            $createdCount = 0;
            $activeCount = 0;
            $inactiveCount = 0;
            $totalCount = count($stops);
            $seenCodes = [];

            foreach ($stops as $stop) {
                $validData = (
                    isset($stop['arretcodelong']) &&
                    isset($stop['nomarret']) &&
                    isset($stop['commune']) &&
                    isset($stop['pays']) &&
                    isset($stop['coordonnees']['lat']) &&
                    isset($stop['coordonnees']['lon'])
                );

                $code = $stop['codedidoc'] ?? null;
                $isActive = 'N';

                if ($stop['actif'] === 'Y' && $validData && $code && !in_array($code, $seenCodes)) {
                    $isActive = 'Y';
                    $seenCodes[] = $code;
                    $activeCount++;
                } else {
                    $inactiveCount++;
                }

                $exists = DB::table('stops')->where('sto_id', $stop['arretcodelong'])->exists();
                if (!$exists) {
                    $createdCount++;
                }

                DB::table('stops')->updateOrInsert(
                    ['sto_id' => $stop['arretcodelong']],
                    [
                        'sto_name' => $stop['nomarret'] ?? null,
                        'sto_municipality' => $stop['commune'] ?? null,
                        'sto_country' => $stop['pays'] ?? null,
                        'sto_latitude' => $stop['coordonnees']['lat'] ?? null,
                        'sto_longitude' => $stop['coordonnees']['lon'] ?? null,
                        'sto_actif' => $isActive,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // Résumé
            $this->info("Total reçu depuis l’API : $totalCount arrêts");
            $this->info("Arrêts actifs (sto_actif = 'Y') : $activeCount");
            $this->info("Arrêts inactifs (sto_actif = 'N') : $inactiveCount");
            $this->info("Nouveaux arrêts insérés : $createdCount");

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
            return 1;
        }
    }
}
