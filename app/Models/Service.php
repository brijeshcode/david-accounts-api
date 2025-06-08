<?php

namespace App\Models;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Service extends Model
{
    use HasFactory, UsesTenantConnection, SoftDeletes, TrackCreatorInfo;

    protected $fillable = [
        'name', 'note', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
