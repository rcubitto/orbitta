<?php

use App\Livewire\Expense;
use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use App\Models\User;
use Illuminate\Support\Carbon;
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
            'categoryId' => Category::factory()->create()->id,
            'type' => 'One-Time',
            'paymentMethod' => 'MP',
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->tap(fn () => $this->assertDatabaseHas(ExpenseModel::class, [
            'external_id' => $attributes['externalId'],
            'date' => $attributes['date'],
            'description' => $attributes['description'],
            'amount' => $attributes['amount'],
            'category_id' => $attributes['categoryId'],
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

it('edits an expense', function () {
    $expense = ExpenseModel::factory()->create([
        'external_id' => fake()->uuid(),
        'amount' => 3000000, // $30,000
    ]);

    $this->assertDatabaseCount(ExpenseModel::class, 1);

    Livewire::actingAs($expense->user)
        ->test(Expense::class)
        // edit
        ->assertSet('editing', null)
        ->call('edit', expense: $expense)
        ->assertSet('editing', $expense)
        ->assertSet('externalId', $expense->external_id)
        ->assertSet('date', $expense->date)
        ->assertSet('description', $expense->description)
        ->assertSet('amount', '30,000')
        ->assertSet('categoryId', $expense->category_id)
        ->assertSet('type', $expense->type)
        ->assertSet('paymentMethod', $expense->payment_method)
        ->assertSet('notes', $expense->notes)
        // update
        ->fill($attributes = [
            'externalId' => '1234567890',
            'date' => Carbon::yesterday(),
            'description' => fake()->paragraph,
            'amount' => '1,500.1',
            'categoryId' => Category::factory()->create()->id,
            'type' => 'One-Time',
            'paymentMethod' => 'VISA',
            'notes' => 'Random notes',
        ])
        ->call('save')
        // assert
        ->tap(function () use ($attributes, $expense) {
            $this->assertDatabaseHas(ExpenseModel::class, [
                'id' => $expense->id,
                'external_id' => $attributes['externalId'],
                'date' => $attributes['date'],
                'description' => $attributes['description'],
                'amount' => 150010,
                'category_id' => $attributes['categoryId'],
                'type' => $attributes['type'],
                'payment_method' => $attributes['paymentMethod'],
                'user_id' => $expense->user_id,
                'notes' => $attributes['notes'],
            ]);

            $this->assertDatabaseCount(ExpenseModel::class, 1);
        })
        ->assertSet('editing', null)
        ->assertSet('externalId', null)
        ->assertSet('date', today())
        ->assertSet('description', null)
        ->assertSet('amount', null)
        ->assertSet('categoryId', null)
        ->assertSet('type', null)
        ->assertSet('paymentMethod', null)
        ->assertSet('notes', null)
        ->assertDispatched('toast-show',
            duration: 5000,
            slots: ['text' => 'Your changes have been saved.'],
            dataset: ['variant' => 'success'],
        );
});
