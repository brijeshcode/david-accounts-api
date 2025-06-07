<?php

use App\Models\Customer;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.customers', 'v1.customers' );

it('can get customer list', function (){

    Customer::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.customers.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Customers List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'note', 'email']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('customers.index');

it('can store a new customer', function(){
    
    $payload = [
        'name' => 'Shiv',
        'starting_balance' => '10',
        'balance' => '1555',
        'email' => 'customer@example.com',
    ];

    $response = asAdmin()->postJson(route('v1.setup.customers.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Customer created successfully']);

    $this->assertDatabaseHas('customers', [
        'email' => 'customer@example.com',
        'name'  => 'Shiv',
    ], 'tenant');

})->group('customers.store');

it('can update a customer', function () {

    $customer = Customer::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.customers.update', $customer->id), [
        'name' => 'Ram',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Customer updated successfully']);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Ram',
    ], 'tenant');
})->group('customers.update');

it('can delete a customer', function () {
    $customer = Customer::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.customers.destroy', $customer->id));

    $response->assertNoContent();

    // $this->assertDatabaseMissing('customers', [
    //     'id' => $customer->id,
    // ]);


    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($customer->fresh()->trashed());

})->group('customers.delete');

it('can validate customer data', function () {
    $response = asAdmin()->postJson(route('v1.setup.customers.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('customers.validation');

it('can validate in detail to each field');