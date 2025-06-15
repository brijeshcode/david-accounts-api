<?php

use App\Models\ExpenseType;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.expenseTypes', 'v1.expenseTypes' );

describe('ExpenseType Model', function () {
    it('can create an expense type', function () {
        $expenseType = ExpenseType::factory()->create([
            'name' => 'Travel',
            'note' => 'Travel related expenses',
            'is_active' => true,
        ]);

        expect($expenseType)
            ->name->toBe('Travel')
            ->note->toBe('Travel related expenses')
            ->is_active->toBeTrue()
            ->parent_id->toBeNull();
    });

    it('can create a child expense type', function () {
        $parent = ExpenseType::factory()->create(['name' => 'Travel']);
        $child = ExpenseType::factory()->create([
            'name' => 'Flight',
            'parent_id' => $parent->id,
        ]);

        expect($child->parent_id)->toBe($parent->id);
        expect($child->parent->name)->toBe('Travel');
        expect($parent->children)->toHaveCount(1);
        expect($parent->children->first()->name)->toBe('Flight');
    });

    it('can check if expense type is parent', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        expect($parent->isParent())->toBeTrue();
        expect($child->isParent())->toBeFalse();
    });

    it('can check if expense type is child', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        expect($parent->isChild())->toBeFalse();
        expect($child->isChild())->toBeTrue();
    });

    it('can check if expense type is root', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        expect($parent->isRoot())->toBeTrue();
        expect($child->isRoot())->toBeFalse();
    });

    it('can get hierarchical level', function () {
        $grandparent = ExpenseType::factory()->create(['name' => 'Office']);
        $parent = ExpenseType::factory()->create([
            'name' => 'Supplies',
            'parent_id' => $grandparent->id,
        ]);
        $child = ExpenseType::factory()->create([
            'name' => 'Stationery',
            'parent_id' => $parent->id,
        ]);

        expect($grandparent->getLevel())->toBe(0);
        expect($parent->getLevel())->toBe(1);
        expect($child->getLevel())->toBe(2);
    });

    it('can get full path', function () {
        $grandparent = ExpenseType::factory()->create(['name' => 'Office']);
        $parent = ExpenseType::factory()->create([
            'name' => 'Supplies',
            'parent_id' => $grandparent->id,
        ]);
        $child = ExpenseType::factory()->create([
            'name' => 'Stationery',
            'parent_id' => $parent->id,
        ]);

        expect($grandparent->getFullPath())->toBe('Office');
        expect($parent->getFullPath())->toBe('Office > Supplies');
        expect($child->getFullPath())->toBe('Office > Supplies > Stationery');
    });

    it('can get descendant ids', function () {
        $parent = ExpenseType::factory()->create();
        $child1 = ExpenseType::factory()->create(['parent_id' => $parent->id]);
        $child2 = ExpenseType::factory()->create(['parent_id' => $parent->id]);
        $grandchild = ExpenseType::factory()->create(['parent_id' => $child1->id]);

        $descendants = $parent->getDescendantIds();

        expect($descendants)->toHaveCount(3);
        expect($descendants)->toContain($child1->id, $child2->id, $grandchild->id);
    });

    it('can get ancestor ids', function () {
        $grandparent = ExpenseType::factory()->create();
        $parent = ExpenseType::factory()->create(['parent_id' => $grandparent->id]);
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $ancestors = $child->getAncestorIds();

        expect($ancestors)->toHaveCount(2);
        expect($ancestors)->toContain($parent->id, $grandparent->id);
    });

    it('can detect circular reference', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        expect($parent->wouldCreateCircularReference($child->id))->toBeTrue();
        expect($child->wouldCreateCircularReference($parent->id))->toBeFalse();
        expect($parent->wouldCreateCircularReference($parent->id))->toBeTrue();
    });

    it('can get root ancestor', function () {
        $grandparent = ExpenseType::factory()->create(['name' => 'Root']);
        $parent = ExpenseType::factory()->create(['parent_id' => $grandparent->id]);
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        expect($child->getRoot()->name)->toBe('Root');
        expect($parent->getRoot()->name)->toBe('Root');
        expect($grandparent->getRoot()->name)->toBe('Root');
    });

    it('soft deletes children when parent is deleted', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $parent->delete();

        expect($parent->trashed())->toBeTrue();
        expect($child->fresh()->trashed())->toBeTrue();
    });

    it('restores children when parent is restored', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $parent->delete();
        $parent->restore();

        expect($parent->trashed())->toBeFalse();
        expect($child->fresh()->trashed())->toBeFalse();
    });
});

describe('ExpenseType Scopes', function () {
    it('can filter active expense types', function () {
        ExpenseType::factory()->create(['is_active' => true]);
        ExpenseType::factory()->create(['is_active' => false]);

        $active = ExpenseType::active()->get();

        expect($active)->toHaveCount(1);
        expect($active->first()->is_active)->toBeTrue();
    });

    it('can filter inactive expense types', function () {
        ExpenseType::factory()->create(['is_active' => true]);
        ExpenseType::factory()->create(['is_active' => false]);

        $inactive = ExpenseType::inactive()->get();

        expect($inactive)->toHaveCount(1);
        expect($inactive->first()->is_active)->toBeFalse();
    });

    it('can filter parent expense types', function () {
        $parent = ExpenseType::factory()->create();
        ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $parents = ExpenseType::parents()->get();

        expect($parents)->toHaveCount(1);
        expect($parents->first()->id)->toBe($parent->id);
    });

    it('can filter child expense types', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $children = ExpenseType::childTypes()->get();

        expect($children)->toHaveCount(1);
        expect($children->first()->id)->toBe($child->id);
    });

    it('can search by name', function () {
        ExpenseType::factory()->create(['name' => 'Travel Expenses']);
        ExpenseType::factory()->create(['name' => 'Office Supplies']);

        $results = ExpenseType::search('Travel')->get();

        expect($results)->toHaveCount(1);
        expect($results->first()->name)->toBe('Travel Expenses');
    });
});

describe('ExpenseType API Controller', function () {
    it('can list expense types', function () {
        ExpenseType::factory(3)->create();

        $response = asAdmin()->getJson(route('v1.setup.expense-type.index'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'parent_id',
                        'note',
                        'is_active',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    });

    it('can create expense type', function () {
        $data = [
            'name' => 'Travel',
            'note' => 'Travel related expenses',
            'is_active' => true,
        ];

        $response = asAdmin()->postJson(route('v1.setup.expense-type.store'), $data);

        $response
            ->assertCreated()
             ->assertJsonFragment(['message' => 'ExpenseType created successfully']);

        $this->assertDatabaseHas('expense_types', $data, 'tenant');
    })->group('expenseType.store');

    it('validates required fields when creating', function () {
        $response = asAdmin()->postJson(route('v1.setup.expense-type.store'), []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates unique name when creating', function () {
        ExpenseType::factory()->create(['name' => 'Travel']);

        $response = asAdmin()->postJson(route('v1.setup.expense-type.store'), [
            'name' => 'Travel',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('can show expense type', function () {
        $expenseType = ExpenseType::factory()->create();

        $response = asAdmin()->getJson(route('v1.setup.expense-type.show', $expenseType->id));

        $response
            ->assertOk()
            ->assertJsonFragment(['id' => $expenseType->id]);
    });

    it('can update expense type', function () {
        $expenseType = ExpenseType::factory()->create(['name' => 'Old Name']);

        $response = asAdmin()->putJson(route('v1.setup.expense-type.update', $expenseType->id), [
            'name' => 'New Name',
            'is_active' => true,
        ]);

        $response
            ->assertOk()
             ->assertJsonFragment(['message' => 'ExpenseType updated successfully']);

        $this->assertDatabaseHas('expense_types', [
            'id' => $expenseType->id,
            'name' => 'New Name',
        ], 'tenant');
    });

    it('prevents circular reference when updating parent', function () {
        $parent = ExpenseType::factory()->create();
        $child = ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $response = asAdmin()->putJson(route('v1.setup.expense-type.update', $parent->id), [
            'name' => $parent->name,
            'parent_id' => $child->id,
            'is_active' => true,
        ]);

        $response->assertUnprocessable();
    });

    it('can delete expense type', function () {
        $expenseType = ExpenseType::factory()->create();

        $response = asAdmin()->deleteJson(route('v1.setup.expense-type.destroy', $expenseType->id));
        $response->assertNoContent();

        $this->assertSoftDeleted('expense_types', ['id' => $expenseType->id], 'tenant');
    });

    it('prevents deletion of expense type with children', function () {
        $parent = ExpenseType::factory()->create();
        ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $response = asAdmin()->deleteJson(route('v1.setup.expense-type.destroy', $parent->id));

        $response->assertUnprocessable();
    });

    it('can restore deleted expense type', function () {
        $expenseType = ExpenseType::factory()->create();
        $expenseType->delete();

        $response = asAdmin()->postJson(route('v1.setup.expense-type.restore', $expenseType->id));

        $response->assertOk();
        $this->assertDatabaseHas('expense_types', [
            'id' => $expenseType->id,
            'deleted_at' => null,
        ], 'tenant');
    });

    it('can toggle expense type status', function () {
        $expenseType = ExpenseType::factory()->create(['is_active' => true]);

        $response = asAdmin()->patchJson(route('v1.setup.expense-type.toggleStatus', $expenseType->id));

        $response->assertOk();
        $this->assertDatabaseHas('expense_types', [
            'id' => $expenseType->id,
            'is_active' => false,
        ], 'tenant');
    });

    it('can get tree structure', function () {
        $parent = ExpenseType::factory()->create(['name' => 'Parent']);
        $child = ExpenseType::factory()->create([
            'name' => 'Child',
            'parent_id' => $parent->id,
        ]);

        $response = asAdmin()->getJson(route('v1.setup.expense-type.tree'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'children' => [
                            '*' => [
                                'id',
                                'name',
                            ]
                        ]
                    ]
                ]
            ]);
    });

    it('can filter by active status', function () {
        ExpenseType::factory()->create(['is_active' => true]);
        ExpenseType::factory()->create(['is_active' => false]);

        $response = asAdmin()->getJson(route('v1.setup.expense-type.index', ['is_active'=>1]));

        $response->assertOk();
        
        $data = $response->json('data');
        expect($data)->toHaveCount(1);
        expect($data[0]['is_active'])->toBeTrue();
    });

    it('can filter parent only expense types', function () {
        $parent = ExpenseType::factory()->create();
        ExpenseType::factory()->create(['parent_id' => $parent->id]);

        $response = asAdmin()->getJson(route('v1.setup.expense-type.index', ['parent_only'=>1]));

        $response->assertOk();
        
        $data = $response->json('data');
        expect($data)->toHaveCount(1);
        expect($data[0]['parent_id'])->toBeNull();
    });

    it('can search expense types', function () {
        ExpenseType::factory()->create(['name' => 'Travel Expenses']);
        ExpenseType::factory()->create(['name' => 'Office Supplies']);

        $response = asAdmin()->getJson(route('v1.setup.expense-type.index', ['search'=>'Travel']));
        $response->assertOk();
        
        $data = $response->json('data');
        expect($data)->toHaveCount(1);
        expect($data[0]['name'])->toBe('Travel Expenses');
    });
})->group('expenseType.controller');