<?php

use App\Models\Service;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.services', 'v1.services' );

it('can get service list', function (){

    Service::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.services.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Services List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'note', 'is_active']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('services.index');

it('can store a new service', function(){
    
    $payload = [
        'name' => 'Shiv', 
    ];

    $response = asAdmin()->postJson(route('v1.setup.services.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Service created successfully']);

    $this->assertDatabaseHas('services', [
        'name' => 'Shiv', 
    ], 'tenant');

})->group('services.store');

it('can update a service', function () {

    $service = Service::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.services.update', $service->id), [
        'name' => 'Ram',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Service updated successfully']);

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Ram',
    ], 'tenant');
})->group('services.update');

it('can delete a service', function () {
    $service = Service::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.services.destroy', $service->id));

    $response->assertNoContent();

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($service->fresh()->trashed());

})->group('services.delete');

it('can validate service data', function () {
    $response = asAdmin()->postJson(route('v1.setup.services.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('services.validation');


it('can validate unique service name', function () {
    $service = Service::factory()->connection('tenant')->create(['name' => 'brijesh']);

    $response = asAdmin()->postJson(route('v1.setup.services.store'), [
        'name' => 'brijesh',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('services.validation');


it('can create a new service with the name of a soft-deleted service', function () {
    // Soft-deleted service
    $service = Service::factory()->create(['name' => 'duplicate-service']);
    $service->delete(); // soft delete

    // Attempt to create a new one with same name
    $response = asAdmin()->postJson(route('v1.setup.services.store'), [
        'name' => 'duplicate-service',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Service created successfully']);

    // Ensure 2 services exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('services', 2, 'tenant');
    $this->assertDatabaseHas('services', [
        'name' => 'duplicate-service',
        'deleted_at' => null,
    ], 'tenant');
})->group('services.validation');



it('fails to update service name if already used in another active service', function () {
    $service1 = Service::factory()->create(['name' => 'Service A']);
    $service2 = Service::factory()->create(['name' => 'Service B']);

    $response = asAdmin()->putJson(route('v1.setup.services.update', $service2), [
        'name' => 'Service A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('services.update');



it('allows updating a service name that was soft-deleted from another record', function () {
    // Create and soft-delete a service with the name "Deleted Service"
    $deletedService = Service::factory()->create(['name' => 'Deleted Service']);
    $deletedService->delete();

    // Create another active service
    $activeService = Service::factory()->create(['name' => 'Active Service']);

    // Attempt to update active service to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.services.update', $activeService), [
        'name' => 'Deleted Service',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Service updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('services', [
        'id' => $activeService->id,
        'name' => 'Deleted Service',
        'deleted_at' => null,
    ], 'tenant');
})->group('services.update');

it('can validate in detail to each field');