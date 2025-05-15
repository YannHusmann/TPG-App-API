<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;

class RouteController extends Controller
{
    public function getAllRoutes()
    {
        $routes = Route::select('rou_id', 'rou_code')
            ->orderBy('rou_code')
            ->get();

        return response()->json([
            'data' => $routes
        ]);
    }
}
