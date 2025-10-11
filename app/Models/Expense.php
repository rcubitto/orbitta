<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    protected static $unguarded = true;

    protected function casts(): array
    {
        return [
            'date' => 'immutable_date',
        ];
    }

    public function categoryBadgeColor(): string
    {
        return match ($this->category) {
            'Dogs' => 'zinc',
            'Drugs' => 'red',
            'Entertainment' => 'orange',
            'Extras' => 'amber',
            'Groceries' => 'yellow',
            'Healthcare' => 'lime',
            'Household' => 'green',
            'Housing' => 'emerald',
            'Online Services' => 'teal',
            'Other' => 'cyan',
            'Taxes & Accounting' => 'sky',
            'Transport' => 'blue',
            'Utilities' => 'indigo',
        };
    }
}
