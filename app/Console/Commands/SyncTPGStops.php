<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncTPGStops extends Command
{
    protected $signature = 'stops:sync';
    protected $description = 'Synchronise les arrêts TPG avec la base de données';

    public function handle()
    {
        $this->info('Téléchargement des arrêts depuis l\'API TPG...');

        $url = 'https://opendata.tpg.ch/api/explore/v2.1/catalog/datasets/arrets/exports/json?lang=fr&timezone=Europe%2FBerlin';

        try {
            $response = Http::get($url);

            if ($response->failed()) {
                $this->error("Erreur lors du téléchargement du fichier JSON.");
                return 1;
            }

            $stops = $response->json();

            $filtered = collect($stops)->filter(function ($stop) {
                return $stop['actif'] === 'Y'
                    && isset($stop['codedidoc'])
                    && isset($stop['coordonnees']['lat'])
                    && isset($stop['coordonnees']['lon']);
            })->unique('codedidoc');

            DB::table('stops')->delete();

            foreach ($filtered as $stop) {
                DB::table('stops')->insert([
                    'sto_id' => $stop['arretcodelong'],
                    'sto_name' => $stop['nomarret'],
                    'sto_municipality' => $stop['commune'],
                    'sto_country' => $stop['pays'],
                    'sto_latitude' => $stop['coordonnees']['lat'],
                    'sto_longitude' => $stop['coordonnees']['lon'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->info("Succes : " . $filtered->count() . " arrêts actifs insérés avec succès.");
            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
            return 1;
        }
    }
}
