<?php

namespace App\Models;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, UsesTenantConnection, SoftDeletes, TrackCreatorInfo;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'on_invoice', 'note', 'active'
    ];
}
