<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncTPGRoutes extends Command
{
    protected $signature = 'routes:sync';
    protected $description = 'Synchronise toutes les lignes TPG avec la base de données';

    public function handle()
    {
        $this->info('Synchronisation des lignes TPG...');

        $url = 'https://opendata.tpg.ch/api/explore/v2.1/catalog/datasets/montees-mensuelles-par-arret-par-ligne/exports/json?lang=fr&timezone=Europe%2FBerlin';

        try {
            $response = Http::timeout(120)->get($url);

            if ($response->failed()) {
                $this->error("Erreur lors du téléchargement du fichier JSON.");
                return 1;
            }

            $data = $response->json();
            $maxDate = null;

            DB::table('route_stop')->truncate();

            // 1ère passe : chercher le mois le plus récent
            foreach ($data as $entry) {
                if (isset($entry['mois']) && ($maxDate === null || $entry['mois'] > $maxDate)) {
                    $maxDate = $entry['mois'];
                }
            }

            if (!$maxDate) {
                $this->error("Aucune date valide trouvée.");
                return 1;
            }

            $routesInserted = 0;
            $relationsInserted = 0;
            $seenRoutes = [];
            $seenRelations = [];

            foreach ($data as $entry) {
                if (($entry['mois'] ?? '') !== $maxDate) continue;

                $routeCode = $entry['ligne'] ?? null;
                $stopCode = $entry['arret_code_long'] ?? null;

                if (!$routeCode || !$stopCode) continue;

                // Insert route
                if (!in_array($routeCode, $seenRoutes)) {
                    DB::table('routes')->updateOrInsert(
                        ['rou_code' => $routeCode],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    $seenRoutes[] = $routeCode;
                    $routesInserted++;
                }

                $routeId = DB::table('routes')->where('rou_code', $routeCode)->value('rou_id');
                $relationKey = "$routeId|$stopCode";

                if ($routeId && !in_array($relationKey, $seenRelations)) {
                    DB::table('route_stop')->updateOrInsert(
                        ['route_id' => $routeId, 'stop_id' => $stopCode],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    $relationsInserted++;
                    $seenRelations[] = $relationKey;
                }
            }

            $this->info("Traitement terminé.");
            $this->info("Routes créées ou mises à jour : $routesInserted");
            $this->info("Relations route <-> arrêt créées : $relationsInserted");

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
            return 1;
        }
    }
}
