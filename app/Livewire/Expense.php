<?php

namespace App\Livewire;

use App\Models\Expense as ExpenseModel;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Expenses'])]
class Expense extends Component
{
    use WithPagination;

    #[Validate('required', 'date')]
    public Carbon $date;
    #[Validate('required')]
    public string $description;
    #[Validate('required', 'numeric')]
    public string $amount;
    #[Validate(['required'])]
    public string $category = '';
    #[Validate('required')]
    public string $type = '';
    #[Validate('required')]
    public string $paymentMethod = '';
    public ?string $notes = null;

    public function mount(): void
    {
        $this->date = today();
    }

    public function render()
    {
        return view('livewire.expense', [
            'expenses' => ExpenseModel::where('user_id', auth()->id())->latest()->paginate(15),
        ]);
    }

    public function save(): void
    {
        $this->validate();

        [$whole, $decimals] = str($this->amount)
            ->remove(',')
            ->explode('.');

        $amountInCents = (int) $whole.str($decimals)->take(2)->padRight(2, '0');

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

        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }
}
