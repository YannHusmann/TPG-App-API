<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use JsonMachine\JsonMachine\JsonMachine;

Route::get('/test-jsonmachine', function () {
    $data = JsonMachine::fromFile(storage_path('app/tpg/montees.json'));
    foreach ($data as $item) {
        dd($item);
    }
});
