<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use Flux\DateRangePreset;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Expenses'])]
class Expense extends Component
{
    use WithPagination;

    #[Session]
    #[Validate('required', 'date')]
    public ?Carbon $date = null;
    #[Validate('required')]
    public string $description;
    #[Validate('required', 'numeric')]
    public string $amount;
    #[Validate(['required', 'numeric', 'exists:categories,id'])]
    public string $categoryId = '';
    #[Validate('required')]
    public string $type = '';
    #[Validate('required')]
    public string $paymentMethod = '';
    public ?string $notes = null;

    public ?ExpenseModel $editing = null;

    #[Session]
    public DateRangePreset $dateRangePreset = DateRangePreset::ThisMonth;

    #[Session]
    public bool $keepAdding = false;

    public $filteredCategories;
    public $filteredTypes;

    public function render()
    {
        return view('livewire.expense', [
            'expenses' => ExpenseModel::where('user_id', auth()->id())
                ->whereBetween('date', $this->dateRangePreset->dates(Carbon::parse('2025-01-01')))
                ->when($this->filteredCategories, function ($query, $values) {
                    $query->whereIn('category_id', $values);
                })
                ->when($this->filteredTypes, function ($query, $values) {
                    $query->whereIn('type', $values);
                })
                ->latest('date')
                ->latest('id')
                ->paginate(15),
        ]);
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::where('is_active', true)->doesntHave('parent')->with('children')->get();
    }

    public static function types(): array
    {
        return [
            'Recurring',
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
                'date' => $this->date,
                'description' => $this->description,
                'amount' => to_cents($this->amount),
                'category_id' => (int) $this->categoryId,
                'type' => $this->type,
                'payment_method' => $this->paymentMethod,
                'notes' => $this->notes,
            ]);
        } else {
            ExpenseModel::create([
                'user_id' => auth()->id(),
                'date' => $this->date,
                'description' => $this->description,
                'amount' => to_cents($this->amount),
                'category_id' => (int) $this->categoryId,
                'type' => $this->type,
                'payment_method' => $this->paymentMethod,
                'notes' => $this->notes,
            ]);
        }

        if (! $this->keepAdding || $this->editing) Flux::modals()->close();
        $this->clear();
        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }

    public function edit(ExpenseModel $expense): void
    {
        $this->editing = $expense;

        $this->fill([
            'date' => Carbon::parse($expense->date),
            'description' => $expense->description,
            'amount' => number_format($expense->amount / 100, 0),
            'categoryId' => $expense->category_id,
            'type' => $expense->type,
            'paymentMethod' => $expense->payment_method,
            'notes' => $expense->notes,
        ]);
    }

    public function clear(): void
    {
        $this->resetValidation();
        $this->resetExcept('date', 'dateRangePreset', 'keepAdding');
        // $this->date = today();
    }

    public function delete($id): void
    {
        dd($id);
    }
}
