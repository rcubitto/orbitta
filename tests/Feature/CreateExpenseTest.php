<?php

use App\Livewire\Expense;
use App\Models\Expense as ExpenseModel;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('the component renders ok', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Expense::class)
        ->assertStatus(200);
});

it('creates a new expense', function () {
    $this->assertDatabaseEmpty(ExpenseModel::class);

    Livewire::actingAs($user = User::factory()->create())
        ->test(Expense::class)
        ->fill($attributes = [
            'externalId' => '123456789',
            'date' => today(),
            'description' => fake()->paragraph,
            'amount' => '25,000.12',
            'category' => 'Groceries',
            'type' => 'One-Time',
            'paymentMethod' => 'MP',
        ])
        ->call('save')
        ->tap(fn () => $this->assertDatabaseHas(ExpenseModel::class, [
            'external_id' => $attributes['externalId'],
            'date' => $attributes['date'],
            'description' => $attributes['description'],
            'amount' => $attributes['amount'],
            'category' => $attributes['category'],
            'type' => $attributes['type'],
            'payment_method' => $attributes['paymentMethod'],
            'user_id' => $user->id,
            'amount' => 2500012,
            'notes' => null,
        ]))
        ->assertDispatched('toast-show',
            duration: 5000,
            slots: ['text' => 'Your changes have been saved.'],
            dataset: ['variant' => 'success'],
        );
});
