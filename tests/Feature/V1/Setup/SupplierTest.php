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

it('can validate in detail to each field');