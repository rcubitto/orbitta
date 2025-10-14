<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'Treats' => 'orange',
            'Extras' => 'amber',
            'Groceries' => 'yellow',
            'Health Care' => 'lime',
            'Household' => 'green',
            'Online Services' => 'teal',
            'Other' => 'cyan',
            'Taxes & Accounting' => 'sky',
            'Transport' => 'blue',
            'Bills & Utilities' => 'indigo',
            default => 'zinc',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
