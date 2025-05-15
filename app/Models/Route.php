<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stop;
use App\Models\Route as RouteModel;

class Route extends Model
{
    protected $primaryKey = 'rou_id';
    public $incrementing = true;

    protected $fillable = [
        'rou_code',
    ];

    public function stops()
    {
        return $this->belongsToMany(Stop::class, 'route_stop', 'route_id', 'stop_id');
    }
}
