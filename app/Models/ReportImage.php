<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportImage extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'path'];

    protected $appends = ['url'];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'rep_id');
    }

    public function getUrlAttribute(): string
    {
        return \Storage::disk('public')->url($this->path);
    }
}
