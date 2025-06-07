<?php 

use App\Models\User;
uses()->group('api', 'v1.api' , 'v1.setup', 'v1.users' );

beforeEach(function () {
    $this->admin = User::factory()->create();
});

it('can get a user list', function () {
    User::factory()->count(3)->create();

    $response = asAdmin($this->admin)->getJson(route('v1.setup.users.index'));

    $response->assertOk()
            ->assertJsonFragment(['message' => 'Users List']) 
            ->assertJsonStructure([
                'data' => [['id', 'name', 'email']],
            ])
            ->assertJsonCount(4, 'data'); // 3 + 1 (admin)
})->group('users.index');


it('can store a new user', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = asAdmin($this->admin)->postJson(route('v1.setup.users.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'User created successfully']);

    expect()->toExistInTenantDb('users', [
        'email' => 'john@example.com',
        'name'  => 'John Doe',
    ]);

})->group('users.store');

it('can update a user', function () {
    $user = User::factory()->create();

    $response = asAdmin($this->admin)->putJson(route('v1.setup.users.update', $user->id), [
        'name' => 'Updated Name',
        'email' => $user->email,
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'User updated successfully']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
    ], 'tenant');
})->group('users.update');

it('can delete a user', function () {
    $user = User::factory()->create();

    $response = asAdmin($this->admin)->deleteJson(route('v1.setup.users.destroy', $user->id));

    $response->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => now(), // Ensure it's soft deleted
    ], 'tenant');

    $this->assertTrue($user->fresh()->trashed());

})->group('users.delete');

it('fails validation on invalid data', function () {
    $response = asAdmin($this->admin)->postJson(route('v1.setup.users.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name', 'email', 'password']);
})->group('users.validation');
