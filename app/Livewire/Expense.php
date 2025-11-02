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

    public ?string $externalId = null;
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

    public $data;
    public ?ExpenseModel $editing = null;
    public DateRangePreset $dateRangePreset;

    #[Session]
    public bool $keepAdding = false;

    public function mount(): void
    {
        $this->dateRangePreset = DateRangePreset::ThisMonth;

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

    public $filteredCategories;

    public function render()
    {
        return view('livewire.expense', [
            'expenses' => ($query = ExpenseModel::where('user_id', auth()->id())
                ->whereBetween('date', $this->dateRangePreset->dates()))
                ->when($this->filteredCategories, function ($query, $values) {
                    $query->whereIn('category_id', $values);
                })
                ->clone()->latest('date')->latest('id')
                ->paginate(15),
            'stats' => [
                'Expenses' => $query->clone()->count(),
                'Total' => '$'.number_format($query->clone()->sum('amount') / 100),
                'One-Time' => '$'.number_format($query->clone()->where('type', 'One-Time')->sum('amount') / 100),
                'Recurring' => '$'.number_format($query->clone()->where('type', 'Recurring')->sum('amount') / 100),
                'Installments' => '$'.number_format($query->clone()->where('type', 'Installment')->sum('amount') / 100),
                'Extras' => '$'.number_format($query->clone()->whereIn('category_id', Category::whereRelation('parent', 'name', 'Extras')->pluck('id'))->sum('amount') / 100),
            ]
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
                'external_id' => filled($this->externalId) ? $this->externalId : null,
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
                'external_id' => filled($this->externalId) ? $this->externalId : null,
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
            'externalId' => $expense->external_id,
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
        $this->resetExcept('date', 'data', 'dateRangePreset', 'keepAdding');
        // $this->date = today();
    }

    public function delete($id): void
    {
        dd($id);
    }
}
