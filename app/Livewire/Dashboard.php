<?php

namespace App\Livewire;

use App\Models\Expense;
use Flux\DateRangePreset;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public DateRangePreset $dateRangePreset;

    public function mount(): void
    {
        $this->dateRangePreset = DateRangePreset::ThisMonth;
    }
    public function render()
    {
        $query = Expense::where('user_id', auth()->id())->whereBetween('date', $this->dateRangePreset->dates())->whereNot('type', 'Installment');

        return view('livewire.dashboard', [
            'count' => $query->count(),
            'total' => $query->sum('amount'),
            'sumByType' => $query->orWhere('type', 'Installment')->selectRaw('type, sum(amount) as total')->groupBy('type')->toBase()->get()->sortByDesc('total'),
            'byCategory' => \App\Models\Category::whereNotNull('parent_id')->with('parent')->withSum([
                'expenses' => function ($query) {
                    $query->where('user_id', auth()->id())->whereBetween('date', $this->dateRangePreset->dates())->whereNot('type', 'Installment');
                },
            ], 'amount')->get()->groupBy('parent.name'),
        ]);
    }
}
