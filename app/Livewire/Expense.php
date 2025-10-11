<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

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
    public ?string $notes;

    public function render()
    {
        return view('livewire.expense');
    }

    public function save(): void
    {
        $this->validate();

        // create

        // notify
    }
}
