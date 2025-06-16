<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stop;

class StopController extends Controller
{
    public function getNearbyStops(Request $request)
    {
        \Log::info('getNearbyStops appelé');
        \Log::info('Données reçues :', $request->all());

        $latitude = $request->query('lat');
        $longitude = $request->query('lon');

        if (!$latitude || !$longitude) {
            \Log::error('Erreur : Latitude et longitude sont manquantes');
            return response()->json(['error' => 'Latitude et longitude sont requises'], 400);
        }

        try {
            $stops = Stop::select('*')
                ->selectRaw("
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(sto_latitude)) *
                        cos(radians(sto_longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(sto_latitude))
                    )) AS distance", [$latitude, $longitude, $latitude])
                ->where('sto_actif', 'Y')
                ->whereNotNull('sto_latitude')
                ->whereNotNull('sto_longitude')
                ->with(['routes' => function ($q) {
                    $q->select('routes.rou_id', 'rou_code');
                }])
                ->orderBy('distance')
                ->limit(5)
                ->get();

            \Log::info('Arrêts trouvés :', $stops->toArray());

            return response()->json(['data' => $stops], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur SQL:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }



    public function getAllStop(Request $request)
    {
        \Log::info('getAllStop appelé');
        \Log::info('Données reçues :', $request->all());

        try {
            $stops = Stop::where('sto_actif', 'Y')
                ->with(['routes' => function ($q) {
                    $q->select('routes.rou_id', 'rou_code');
                }])
                ->orderBy('sto_name', 'asc')
                ->get();

            \Log::info('Arrêts trouvés :', $stops->toArray());

            return response()->json(['data' => $stops], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur SQL:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }


    public function getStopByName(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json(['error' => 'Le paramètre de recherche est requis.'], 400);
        }

        $stops = Stop::where('sto_actif', 'Y')
            ->where('sto_name', 'like', '%' . $query . '%')
            ->with(['routes' => function ($q) {
                $q->select('routes.rou_id', 'rou_code');
            }])
            ->get();

        return response()->json(['data' => $stops], 200);
    }


    public function getRoutes(Stop $stop)
    {
        return response()->json([
            'stop' => $stop->sto_name,
            'routes' => $stop->routes()->select('rou_id', 'rou_code')->get()
        ]);
    }

}
