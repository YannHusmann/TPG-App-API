<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    use HasFactory;

    protected $primaryKey = 'sto_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sto_id',
        'sto_name',
        'sto_municipality',
        'sto_country',
        'sto_latitude',
        'sto_longitude',
        'sto_actif',
    ];
    public function routes()
        {
            return $this->belongsToMany(Route::class, 'route_stop', 'stop_id', 'route_id');
        }

}
