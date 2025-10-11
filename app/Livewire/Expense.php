<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Expense as ExpenseModel;

#[Layout('components.layouts.app', ['title' => 'Expenses'])]
class Expense extends Component
{
    #[Validate('required', 'date')]
    public Carbon $date;
    #[Validate('required')]
    public string $description;
    #[Validate('required', 'numeric')]
    public string $amount;
    #[Validate(['required'])]
    public string $category;
    #[Validate('required')]
    public string $type;
    #[Validate('required')]
    public string $paymentMethod;
    public ?string $notes = null;

    public function mount(): void
    {
        $this->date = today();
    }

    public function render()
    {
        return view('livewire.expense');
    }

    public function save(): void
    {
        $this->validate();

        [$whole, $decimals] = str($this->amount)
            ->remove(',')
            ->explode('.');

        $amountInCents = (int) $whole.str($decimals)->take(2);

        ExpenseModel::create([
            'user_id' => auth()->id(),
            'date' => $this->date,
            'description' => $this->description,
            'amount' => $amountInCents,
            'category' => $this->category,
            'type' => $this->type,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
        ]);

        $this->reset('description', 'amount', 'category', 'type', 'paymentMethod', 'notes');
        $this->date = today();

        // notify
    }
}
