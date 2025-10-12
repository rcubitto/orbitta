<?php

namespace App\Livewire;

use App\Models\Expense as ExpenseModel;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Expenses'])]
class Expense extends Component
{
    use WithPagination;

    public ?string $externalId = null;
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

    public $data;

    public function mount(): void
    {
        $this->date = today();

        $this->data = ExpenseModel::where('user_id', auth()->id())
                ->whereMonth('date', today()->month)
                ->whereYear('date', today()->year)
                ->select('date')
                ->selectRaw('sum(amount) as sum')
                ->groupBy('date')
                ->get()
                ->map(fn ($attributes) => [
                    'date' => $attributes->date->toDateString(),
                    'amount' => $attributes->sum / 100,
                ])->toArray();
    }

    public function render()
    {
        return view('livewire.expense', [
            'expenses' => ExpenseModel::where('user_id', auth()->id())->latest()->paginate(15),
        ]);
    }

    #[Computed]
    public function current(): Collection
    {
        return ExpenseModel::where('user_id', auth()->id())
            ->whereMonth('date', today()->month)
            ->whereYear('date', today()->year)
            ->latest()
            ->get();
    }

    #[Computed]
    public function categories(): array
    {
        return [
            'Dogs',
            'Drugs',
            'Entertainment',
            'Extras',
            'Groceries',
            'Healthcare',
            'Household',
            'Housing',
            'Online Services',
            'Other',
            'Taxes & Accounting',
            'Transport',
            'Utilities',
        ];
    }

    public function save(): void
    {
        $this->validate();

        ExpenseModel::create([
            'user_id' => auth()->id(),
            'external_id' => filled($this->externalId) ? $this->externalId : null,
            'date' => $this->date,
            'description' => $this->description,
            'amount' => to_cents($this->amount),
            'category' => $this->category,
            'type' => $this->type,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
        ]);

        $this->reset('externalId', 'description', 'amount', 'category', 'type', 'paymentMethod', 'notes');
        $this->date = today();

        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }
}
