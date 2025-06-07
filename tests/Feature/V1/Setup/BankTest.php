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

it('can validate in detail to each field');