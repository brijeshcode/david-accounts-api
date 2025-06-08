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

it('can validate unique customer name', function () {
    $customer = Customer::factory()->connection('tenant')->create(['name' => 'brijesh']);

    $response = asAdmin()->postJson(route('v1.setup.customers.store'), [
        'name' => 'brijesh',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('customers.validation');


it('can create a new customer with the name of a soft-deleted customer', function () {
    // Soft-deleted customer
    $customer = Customer::factory()->create(['name' => 'duplicate-customer']);
    $customer->delete(); // soft delete

    // Attempt to create a new one with same name
    $response = asAdmin()->postJson(route('v1.setup.customers.store'), [
        'name' => 'duplicate-customer',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Customer created successfully']);

    // Ensure 2 customers exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('customers', 2, 'tenant');
    $this->assertDatabaseHas('customers', [
        'name' => 'duplicate-customer',
        'deleted_at' => null,
    ], 'tenant');
})->group('customers.validation');



it('fails to update customer name if already used in another active customer', function () {
    $customer1 = Customer::factory()->create(['name' => 'Customer A']);
    $customer2 = Customer::factory()->create(['name' => 'Customer B']);

    $response = asAdmin()->putJson(route('v1.setup.customers.update', $customer2), [
        'name' => 'Customer A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('customers.update');



it('allows updating a customer name that was soft-deleted from another record', function () {
    // Create and soft-delete a customer with the name "Deleted Customer"
    $deletedService = Customer::factory()->create(['name' => 'Deleted Customer']);
    $deletedService->delete();

    // Create another active customer
    $activeService = Customer::factory()->create(['name' => 'Active Customer']);

    // Attempt to update active customer to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.customers.update', $activeService), [
        'name' => 'Deleted Customer',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Customer updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('customers', [
        'id' => $activeService->id,
        'name' => 'Deleted Customer',
        'deleted_at' => null,
    ], 'tenant');
})->group('customers.update');

it('can validate in detail to each field');