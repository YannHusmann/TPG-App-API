<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'rep_id';

    protected $fillable = [
        'rep_use_id',
        'rep_sto_id',
        'rep_rou_id',
        'rep_message',
        'rep_status',
    ];

    /**
     * L'utilisateur qui a soumis le signalement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rep_use_id', 'use_id');
    }

    /**
     * L'arrêt concerné par le signalement.
     */
    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'rep_sto_id', 'sto_id');
    }

    /**
     * La ligne concernée par le signalement (si applicable).
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'rep_rou_id', 'rou_id');
    }
} 
