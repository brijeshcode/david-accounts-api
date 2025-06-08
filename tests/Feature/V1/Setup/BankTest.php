<?php

use App\Models\Bank;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.banks', 'v1.banks' );

it('can get bank list', function (){

    Bank::class::factory()->count(3)->create();
    $response = asAdmin()->getJson(route('v1.setup.banks.index'));

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Banks List'])
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'note', 'account_no']],
             ])
             ->assertJsonCount(3, 'data')
             ;
})->group('banks.index');

it('can store a new bank', function(){
    
    $payload = [
        'name' => 'pnb',
        'starting_balance' => '10',
        'balance' => '1555',
        'account_no' => '007154877454',
    ];

    $response = asAdmin()->postJson(route('v1.setup.banks.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Bank created successfully']);

    $this->assertDatabaseHas('banks', [
        'account_no' => '007154877454',
        'name'  => 'pnb',
    ], 'tenant');

})->group('banks.store');

it('can update a bank', function () {

    $bank = Bank::factory()->create();
    $response = asAdmin()->putJson(route('v1.setup.banks.update', $bank->id), [
        'name' => 'Updated Name',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Bank updated successfully']);

    $this->assertDatabaseHas('banks', [
        'id' => $bank->id,
        'name' => 'Updated Name',
    ], 'tenant');
})->group('banks.update');

it('can delete a bank', function () {
    $bank = Bank::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.banks.destroy', $bank->id));

    $response->assertNoContent();

    // $this->assertDatabaseMissing('banks', [
    //     'id' => $bank->id,
    // ]);


    $this->assertDatabaseHas('banks', [
        'id' => $bank->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($bank->fresh()->trashed());

})->group('banks.delete');

it('can validate bank data', function () {
    $response = asAdmin()->postJson(route('v1.setup.banks.store'), [
        'name' => '',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('banks.validation');

it('can validate unique bank name', function () {
    $bank = Bank::factory()->connection('tenant')->create(['name' => 'pnb']);

    $response = asAdmin()->postJson(route('v1.setup.banks.store'), [
        'name' => 'pnb',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);

})->group('banks.validation');


it('can create a new bank with the name of a soft-deleted bank', function () {
    // Soft-deleted bank
    $bank = Bank::factory()->create(['name' => 'duplicate-bank']);
    $bank->delete(); // soft delete

    // Attempt to create a new one with same name
    $response = asAdmin()->postJson(route('v1.setup.banks.store'), [
        'name' => 'duplicate-bank',
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Bank created successfully']);

    // Ensure 2 banks exist (1 soft deleted, 1 active)
    $this->assertDatabaseCount('banks', 2, 'tenant');
    $this->assertDatabaseHas('banks', [
        'name' => 'duplicate-bank',
        'deleted_at' => null,
    ], 'tenant');
})->group('banks.validation');



it('fails to update bank name if already used in another active bank', function () {
    $bank1 = Bank::factory()->create(['name' => 'Bank A']);
    $bank2 = Bank::factory()->create(['name' => 'Bank B']);

    $response = asAdmin()->putJson(route('v1.setup.banks.update', $bank2), [
        'name' => 'Bank A',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
})->group('banks.update');



it('allows updating a bank name that was soft-deleted from another record', function () {
    // Create and soft-delete a bank with the name "Deleted Bank"
    $deletedBank = Bank::factory()->create(['name' => 'Deleted Bank']);
    $deletedBank->delete();

    // Create another active bank
    $activeBank = Bank::factory()->create(['name' => 'Active Bank']);

    // Attempt to update active bank to the same name as the soft-deleted one
    $response = asAdmin()->putJson(route('v1.setup.banks.update', $activeBank), [
        'name' => 'Deleted Bank',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Bank updated successfully']);

    // Make sure name is updated in the database
    $this->assertDatabaseHas('banks', [
        'id' => $activeBank->id,
        'name' => 'Deleted Bank',
        'deleted_at' => null,
    ], 'tenant');
})->group('banks.update');

it('can validate in detail to each field');