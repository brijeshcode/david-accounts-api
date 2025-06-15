<?php

use App\Models\ExpenseArticle;
use App\Models\ExpenseType;

uses()->group('api', 'v1.api' , 'v1.setup', 'v1.setup.expenseArticles', 'v1.expenseArticles' );

beforeEach(function () {
    // optional: seed roles, permissions, etc.
    $this->user = \App\Models\User::factory()->create();
    $this->actingAs($this->user); // Sanctum or Laravel Auth
});

it('can list expense articles', function () {
    ExpenseArticle::factory()->count(3)->create();

    $response = asAdmin()->getJson(route('v1.setup.expense-articles.index'));

    $response->assertOk()
             ->assertJsonCount(3, 'data');
});

it('can create an expense article with valid data', function () {
    $expenseType = ExpenseType::factory()->create();
    $payload = [
        'expense_type_id' => $expenseType->id,
        'name' => 'Internet Charges',
        'unit' => 'Monthly',
        'note' => 'Broadband bills'
    ];

    $response = asAdmin()->postJson(route('v1.setup.expense-articles.store'), $payload);

    $response->assertCreated()
             ->assertJsonFragment(['message' => 'Expense article created successfully']);

    $this->assertDatabaseHas('expense_articles', ['name' => 'Internet Charges'], 'tenant');
});

it('fails to create expense article with invalid data', function () {
    $response = asAdmin()->postJson(route('v1.setup.expense-articles.store'), []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name', 'unit']);
});

it('can show a single expense article', function () {
    $article = ExpenseArticle::factory()->create();

    $response = asAdmin()->getJson(route('v1.setup.expense-articles.show', $article->id));

    $response->assertOk()
             ->assertJsonFragment(['id' => $article->id]);
});

it('can update an expense article', function () {
    $article = ExpenseArticle::factory()->create();

    $response = asAdmin()->putJson(route('v1.setup.expense-articles.update', $article->id), [
        'name' => 'Updated Name',
        'unit' => 'Daily',
        'note' => 'Updated note',
    ]);

    $response->assertOk()
             ->assertJsonFragment(['message' => 'Expense article updated successfully']);

    $this->assertDatabaseHas('expense_articles', ['name' => 'Updated Name'], 'tenant');
});

it('can delete an expense article', function () {
    $article = ExpenseArticle::factory()->create();

    $response = asAdmin()->deleteJson(route('v1.setup.expense-articles.destroy', $article->id));

    $response->assertNoContent();

    $this->assertDatabaseMissing('expense_articles', ['id' => $article->id]);
});
