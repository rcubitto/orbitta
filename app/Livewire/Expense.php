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
    public ?ExpenseModel $editing = null;

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

    public static function categories(): array
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

    public static function types(): array
    {
        return [
            'Recurring (Fixed)',
            'Recurring (Variable)',
            'One-Time',
            'Installment',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'VISA',
            'Master',
            'MP',
            'Cash',
            'Bank Transfer',
            'Auto Debit',
            'PagoMisCuentas',
            'PayPal',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editing) {
            $this->editing->update([
                'external_id' => filled($this->externalId) ? $this->externalId : null,
                'date' => $this->date,
                'description' => $this->description,
                'amount' => to_cents($this->amount),
                'category' => $this->category,
                'type' => $this->type,
                'payment_method' => $this->paymentMethod,
                'notes' => $this->notes,
            ]);
        } else {
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
        }

        $this->clear();
        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }

    public function edit(ExpenseModel $expense): void
    {
        $this->editing = $expense;

        $this->fill([
            'externalId' => $expense->external_id,
            'date' => Carbon::parse($expense->date),
            'description' => $expense->description,
            'amount' => number_format($expense->amount / 100, 0),
            'category' => $expense->category,
            'type' => $expense->type,
            'paymentMethod' => $expense->payment_method,
            'notes' => $expense->notes,
        ]);
    }

    public function clear(): void
    {
        $this->resetValidation();
        $this->resetExcept('date', 'data');
        $this->date = today();
    }
}
