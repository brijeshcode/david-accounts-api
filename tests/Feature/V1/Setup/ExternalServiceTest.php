<?php

use App\Models\ExternalService;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.externalServices', 'v1.externalServices' );

it('can get externalService list', function (){

    ExternalService::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.externalServices.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'ExternalServices List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'note']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('externalServices.index');

it('can store a new externalService', function(){
    
    $payload = [
        'name' => 'Shiv',
    ];

    $response = asAdmin()->postJson(route('v1.setup.externalServices.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'ExternalService created successfully']);

    $this->assertDatabaseHas('external_services', [
        'name'  => 'Shiv',
    ], 'tenant');

})->group('externalServices.store');

it('can update a externalService', function () {

    $externalService = ExternalService::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.externalServices.update', $externalService->id), [
        'name' => 'Ram',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'ExternalService updated successfully']);

    $this->assertDatabaseHas('external_services', [
        'id' => $externalService->id,
        'name' => 'Ram',
    ], 'tenant');
})->group('externalServices.update');

it('can delete a externalService', function () {
    $externalService = ExternalService::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.externalServices.destroy', $externalService->id));

    $response->assertNoContent();

    $this->assertDatabaseHas('external_services', [
        'id' => $externalService->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($externalService->fresh()->trashed());

})->group('externalServices.delete');

it('can validate externalService data', function () {
    $response = asAdmin()->postJson(route('v1.setup.externalServices.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('externalServices.validation');


it('can validate unique externalService name', function () {
    $externalService = ExternalService::factory()->connection('tenant')->create(['name' => 'brijesh']);

    $response = asAdmin()->postJson(route('v1.setup.externalServices.store'), [
        'name' => 'brijesh',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('externalServices.validation');


it('can create a new externalService with the name of a soft-deleted externalService', function () {
    // Soft-deleted externalService
    $externalService = ExternalService::factory()->create(['name' => 'duplicate-externalService']);
    $externalService->delete(); // soft delete

    $response = asAdmin()->postJson(route('v1.setup.externalServices.store'), [
        'name' => 'duplicate-externalService',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'ExternalService created successfully']);

    // Ensure 2 externalServices exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('external_services', 2, 'tenant');
    $this->assertDatabaseHas('external_services', [
        'name' => 'duplicate-externalService',
        'deleted_at' => null,
    ], 'tenant');
})->group('externalServices.validation');


it('fails to update externalService name if already used in another active externalService', function () {
    $externalService1 = ExternalService::factory()->create(['name' => 'ExternalService A']);
    $externalService2 = ExternalService::factory()->create(['name' => 'ExternalService B']);

    $response = asAdmin()->putJson(route('v1.setup.externalServices.update', $externalService2), [
        'name' => 'ExternalService A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('externalServices.update');



it('allows updating a externalService name that was soft-deleted from another record', function () {
    // Create and soft-delete a externalService with the name "Deleted ExternalService"
    $deletedService = ExternalService::factory()->create(['name' => 'Deleted ExternalService']);
    $deletedService->delete();

    // Create another active externalService
    $activeService = ExternalService::factory()->create(['name' => 'Active ExternalService']);

    // Attempt to update active externalService to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.externalServices.update', $activeService), [
        'name' => 'Deleted ExternalService',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'ExternalService updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('external_services', [
        'id' => $activeService->id,
        'name' => 'Deleted ExternalService',
        'deleted_at' => null,
    ], 'tenant');
})->group('externalServices.update');

it('can validate in detail to each field');