<?php

use App\Models\Warehouse;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.warehouses', 'v1.warehouses' );

it('can get warehouse list', function (){

    Warehouse::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.warehouses.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Warehouses List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'address', 'phone']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('warehouses.index');

it('can store a new warehouse', function(){
    
    $payload = [
        'name' => 'Shiv',
        'address' => '10',
    ];

    $response = asAdmin()->postJson(route('v1.setup.warehouses.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Warehouse created successfully']);

    $this->assertDatabaseHas('warehouses', [
        'address' => '10',
        'name'  => 'Shiv',
    ], 'tenant');

})->group('warehouses.store');

it('can update a warehouse', function () {

    $warehouse = Warehouse::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.warehouses.update', $warehouse->id), [
        'name' => 'Ram',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Warehouse updated successfully']);

    $this->assertDatabaseHas('warehouses', [
        'id' => $warehouse->id,
        'name' => 'Ram',
    ], 'tenant');
})->group('warehouses.update');

it('can delete a warehouse', function () {
    $warehouse = Warehouse::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.warehouses.destroy', $warehouse->id));

    $response->assertNoContent();

    $this->assertDatabaseHas('warehouses', [
        'id' => $warehouse->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($warehouse->fresh()->trashed());

})->group('warehouses.delete');

it('can validate warehouse data', function () {
    $response = asAdmin()->postJson(route('v1.setup.warehouses.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('warehouses.validation');


it('can validate unique warehouse name', function () {
    $warehouse = Warehouse::factory()->connection('tenant')->create(['name' => 'brijesh']);

    $response = asAdmin()->postJson(route('v1.setup.warehouses.store'), [
        'name' => 'brijesh',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('warehouses.validation');


it('can create a new warehouse with the name of a soft-deleted warehouse', function () {
    // Soft-deleted warehouse
    $warehouse = Warehouse::factory()->create(['name' => 'duplicate-warehouse']);
    $warehouse->delete(); // soft delete

    $response = asAdmin()->postJson(route('v1.setup.warehouses.store'), [
        'name' => 'duplicate-warehouse',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Warehouse created successfully']);

    // Ensure 2 warehouses exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('warehouses', 2, 'tenant');
    $this->assertDatabaseHas('warehouses', [
        'name' => 'duplicate-warehouse',
        'deleted_at' => null,
    ], 'tenant');
})->group('warehouses.validation');


it('fails to update warehouse name if already used in another active warehouse', function () {
    $warehouse1 = Warehouse::factory()->create(['name' => 'Warehouse A']);
    $warehouse2 = Warehouse::factory()->create(['name' => 'Warehouse B']);

    $response = asAdmin()->putJson(route('v1.setup.warehouses.update', $warehouse2), [
        'name' => 'Warehouse A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('warehouses.update');



it('allows updating a warehouse name that was soft-deleted from another record', function () {
    // Create and soft-delete a warehouse with the name "Deleted Warehouse"
    $deletedService = Warehouse::factory()->create(['name' => 'Deleted Warehouse']);
    $deletedService->delete();

    // Create another active warehouse
    $activeService = Warehouse::factory()->create(['name' => 'Active Warehouse']);

    // Attempt to update active warehouse to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.warehouses.update', $activeService), [
        'name' => 'Deleted Warehouse',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Warehouse updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('warehouses', [
        'id' => $activeService->id,
        'name' => 'Deleted Warehouse',
        'deleted_at' => null,
    ], 'tenant');
})->group('warehouses.update');

it('can validate in detail to each field');