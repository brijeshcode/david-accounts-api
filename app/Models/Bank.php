<?php

namespace App\Models;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;

class Bank extends Model
{
    /** @use HasFactory<\Database\Factories\BankFactory> */
    use HasFactory, SoftDeletes, UsesTenantConnection, TrackCreatorInfo;

    protected $fillable = [
        'name',
        'starting_balance',
        'balance',
        'address',
        'account_no',
        'note',
        'is_active',
        'created_by_id',
        'created_by_ip',
        'created_by_agent',     
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
