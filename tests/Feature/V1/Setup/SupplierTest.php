<?php

use App\Models\Supplier;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.suppliers', 'v1.suppliers' );

it('can get supplier list', function (){

    Supplier::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.suppliers.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Suppliers List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'note', 'email']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('suppliers.index');

it('can store a new supplier', function(){
    
    $payload = [
        'name' => 'Shiv',
        'starting_balance' => '10',
        'balance' => '1555',
        'email' => 'supplier@example.com',
    ];

    $response = asAdmin()->postJson(route('v1.setup.suppliers.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Supplier created successfully']);

    $this->assertDatabaseHas('suppliers', [
        'email' => 'supplier@example.com',
        'name'  => 'Shiv',
    ], 'tenant');

})->group('suppliers.store');

it('can update a supplier', function () {

    $supplier = Supplier::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.suppliers.update', $supplier->id), [
        'name' => 'Ram',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Supplier updated successfully']);

    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'name' => 'Ram',
    ], 'tenant');
})->group('suppliers.update');

it('can delete a supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.suppliers.destroy', $supplier->id));

    $response->assertNoContent();

    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($supplier->fresh()->trashed());

})->group('suppliers.delete');

it('can validate supplier data', function () {
    $response = asAdmin()->postJson(route('v1.setup.suppliers.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('suppliers.validation');


it('can validate unique supplier name', function () {
    $supplier = Supplier::factory()->connection('tenant')->create(['name' => 'brijesh']);

    $response = asAdmin()->postJson(route('v1.setup.suppliers.store'), [
        'name' => 'brijesh',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('suppliers.validation');


it('can create a new supplier with the name of a soft-deleted supplier', function () {
    // Soft-deleted supplier
    $supplier = Supplier::factory()->create(['name' => 'duplicate-supplier']);
    $supplier->delete(); // soft delete

    // Attempt to create a new one with same name
    $response = asAdmin()->postJson(route('v1.setup.suppliers.store'), [
        'name' => 'duplicate-supplier',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Supplier created successfully']);

    // Ensure 2 suppliers exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('suppliers', 2, 'tenant');
    $this->assertDatabaseHas('suppliers', [
        'name' => 'duplicate-supplier',
        'deleted_at' => null,
    ], 'tenant');
})->group('suppliers.validation');



it('fails to update supplier name if already used in another active supplier', function () {
    $supplier1 = Supplier::factory()->create(['name' => 'Supplier A']);
    $supplier2 = Supplier::factory()->create(['name' => 'Supplier B']);

    $response = asAdmin()->putJson(route('v1.setup.suppliers.update', $supplier2), [
        'name' => 'Supplier A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('suppliers.update');



it('allows updating a supplier name that was soft-deleted from another record', function () {
    // Create and soft-delete a supplier with the name "Deleted Supplier"
    $deletedService = Supplier::factory()->create(['name' => 'Deleted Supplier']);
    $deletedService->delete();

    // Create another active supplier
    $activeService = Supplier::factory()->create(['name' => 'Active Supplier']);

    // Attempt to update active supplier to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.suppliers.update', $activeService), [
        'name' => 'Deleted Supplier',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Supplier updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('suppliers', [
        'id' => $activeService->id,
        'name' => 'Deleted Supplier',
        'deleted_at' => null,
    ], 'tenant');
})->group('suppliers.update');

it('can validate in detail to each field');