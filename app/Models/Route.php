<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $primaryKey = 'rou_id';
    public $incrementing = true;
    protected $fillable = ['rou_id']; // adapte si tu as d'autres colonnes
}
