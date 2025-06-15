<?php

namespace App\Http\Resources\V1\Setup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'note' => $this->note,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            
            // Relationships
            'parent' => new ExpenseTypeResource($this->whenLoaded('parent')),
            'children' => ExpenseTypeResource::collection($this->whenLoaded('children')),
            
            // Computed attributes
            'has_children' => $this->when($this->relationLoaded('children'), 
                fn() => $this->children->isNotEmpty()
            ),
            'children_count' => $this->when($this->relationLoaded('children'), 
                fn() => $this->children->count()
            ),
            'level' => $this->when($this->parent_id !== null, $this->getLevel()),
            'full_path' => $this->getFullPath(),
        ];
    }

    /**
     * Get the hierarchical level of the expense type.
     */
    private function getLevel(): int
    {
        $level = 0;
        $current = $this->resource;

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
    private function getFullPath(): string
    {
        $path = [$this->name];
        $current = $this->resource;

        while ($current->parent_id !== null && $current->parent) {
            $path[] = $current->parent->name;
            $current = $current->parent;
            
            // Prevent infinite loop
            if (count($path) > 10) break;
        }

        return implode(' > ', array_reverse($path));
    }
}
