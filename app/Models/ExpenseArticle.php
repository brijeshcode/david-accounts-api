<?php

namespace App\Models;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ExpenseArticle extends Model
{
    use HasFactory, UsesTenantConnection, SoftDeletes, TrackCreatorInfo;

    protected $fillable = [
        'expense_type_id',
        'name',
        'unit',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expense_type_id' => 'integer',
    ];

    /**
     * Get the expense type that owns the expense article.
     */
    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    /**
     * Scope a query to only include active expense articles.
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive expense articles.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }
}
