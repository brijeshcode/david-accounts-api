<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use  UsesTenantConnection, HasFactory, Notifiable, HasApiTokens, SoftDeletes, TrackCreatorInfo;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id',
        'profile_photo_path',
        'note',
        'is_active',
        'created_by_id',
        'created_by_ip',
        'created_by_agent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
