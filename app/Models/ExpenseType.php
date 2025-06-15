<?php

namespace App\Models;

use App\Traits\TrackCreatorInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ExpenseType extends Model
{
    use HasFactory, UsesTenantConnection, SoftDeletes, TrackCreatorInfo;

    protected $fillable = [
        'name',
        'parent_id',
        'note',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent expense type.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'parent_id');
    }

    /**
     * Get the child expense types.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ExpenseType::class, 'parent_id');
    }

    /**
     * Get all active child expense types.
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.).
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Scope to get only active expense types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only inactive expense types.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to get only parent expense types (no parent_id).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only child expense types (has parent_id).
     */
    public function scopeChildTypes($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Check if this expense type is a parent (has children).
     */
    public function isParent(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if this expense type is a child (has parent).
     */
    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this expense type is a root (no parent).
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get the hierarchical level (0 for root, 1 for first level, etc.).
     */
    public function getLevel(): int
    {
        $level = 0;
        $current = $this;

        while ($current->parent_id !== null) {
            $level++;
            $current = $current->parent;
            
            // Prevent infinite loop
            if ($level > 10) break;
        }

        return $level;
    }

    /**
     * Get the full hierarchical path.
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $current = $this;

        while ($current->parent_id !== null && $current->parent) {
            $path[] = $current->parent->name;
            $current = $current->parent;
            
            // Prevent infinite loop
            if (count($path) > 10) break;
        }

        return implode(' > ', array_reverse($path));
    }

    /**
     * Get all descendant IDs.
     */
    public function getDescendantIds(): array
    {
        $descendants = [];
        $children = $this->children;

        foreach ($children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $child->getDescendantIds());
        }

        return $descendants;
    }

    /**
     * Get all ancestor IDs.
     */
    public function getAncestorIds(): array
    {
        $ancestors = [];
        $current = $this;

        while ($current->parent_id !== null && $current->parent) {
            $ancestors[] = $current->parent->id;
            $current = $current->parent;
            
            // Prevent infinite loop
            if (count($ancestors) > 10) break;
        }

        return $ancestors;
    }

    /**
     * Check if setting a parent would create a circular reference.
     */
    public function wouldCreateCircularReference($parentId): bool
    {
        if ($parentId === $this->id) {
            return true;
        }

        $descendants = $this->getDescendantIds();
        return in_array($parentId, $descendants);
    }

    /**
     * Get the root ancestor.
     */
    public function getRoot(): ExpenseType
    {
        $current = $this;

        while ($current->parent_id !== null && $current->parent) {
            $current = $current->parent;
        }

        return $current;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting, also delete all children (cascade soft delete)
        static::deleting(function ($expenseType) {
            if ($expenseType->isForceDeleting()) {
                // Force delete children
                $expenseType->children()->forceDelete();
            } else {
                // Soft delete children
                $expenseType->children()->delete();
            }
        });

        // When restoring, also restore all children
        static::restoring(function ($expenseType) {
            $expenseType->children()->withTrashed()->restore();
        });
    }
}
