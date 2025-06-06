<?php 

use App\Models\User;
use function Pest\Laravel\postJson;

uses()->group('api', 'v1.api' , 'v1.auth', 'v1.login' );


beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('password123'),
    ]);
});

it('can login with valid credentials', function () {
    $payload = [
        'email' => 'user@example.com',
        'password' => 'password123',
    ];

    $response = postJson(route('v1.login'), $payload);

    $response->assertOk()
             ->assertJsonStructure([
                 'message',
                 'data' => [
                     'user' => ['id', 'name', 'email'],
                     'token'
                 ]
             ]);
})->group('auth.login');

it('fails login with invalid credentials', function () {
    $payload = [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ];

    $response = postJson(route('v1.login'), $payload);

    $response->assertUnauthorized()
             ->assertJsonFragment(['message' => 'Invalid credentials']);
})->group('auth.login');

it('fails login with missing fields', function () {
    $response = postJson(route('v1.login'), []);
    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['email', 'password'])
             ;
})->group('auth.login');
