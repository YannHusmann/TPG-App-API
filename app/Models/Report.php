<?php

namespace App\Models;

use App\Enums\ReportType;
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
        'rep_type',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'rep_type' => ReportType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rep_use_id', 'use_id');
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'rep_sto_id', 'sto_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'rep_rou_id', 'rou_id');
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class, 'report_id', 'rep_id');
    }

}
