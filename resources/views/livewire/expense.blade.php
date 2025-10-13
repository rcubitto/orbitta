<div>
    {{-- Stats --}}
    <div class="hidden gap-10">
        <div class="space-y-6">
            <flux:card class="overflow-hidden min-w-[12rem]">
                <flux:text>Total ({{ today()->format('M') }})</flux:text>
                <flux:heading size="xl" class="mt-2 tabular-nums">
                    ${{ number_format($total = $this->current->sum('amount') / 100) }}
                </flux:heading>
            </flux:card>
            <flux:card class="overflow-hidden min-w-[12rem]">
                <flux:text class="mb-3">One-Time ({{ today()->format('M') }})</flux:text>
                <flux:heading size="xl" class="mt-2 tabular-nums">
                    ${{ number_format($oneTime = $this->current->where('type', 'One-Time')->sum('amount') / 100) }}
                </flux:heading>
                <span class="text-zinc-300 text-sm">{{ number_format($total ? ($oneTime / $total * 100) : 0, decimals: 2) }}%</span>
            </flux:card>
        </div>
        <div class="w-1/2">
            <flux:chart wire:model="data" class="aspect-3/1">
                <flux:chart.svg>
                    <flux:chart.line field="amount" class="text-pink-500 dark:text-pink-400" />
                    <flux:chart.point field="amount" class="text-pink-400 dark:text-pink-400" />

                    <flux:chart.axis axis="x" field="date">
                        <flux:chart.axis.line />
                        <flux:chart.axis.tick />
                    </flux:chart.axis>

                    <flux:chart.axis axis="y">
                        <flux:chart.axis.grid />
                        <flux:chart.axis.tick />
                    </flux:chart.axis>

                    <flux:chart.cursor />
                </flux:chart.svg>

                <flux:chart.tooltip>
                    <flux:chart.tooltip.heading field="date" :format="['year' => 'numeric', 'month' => 'numeric', 'day' => 'numeric']" />
                    <flux:chart.tooltip.value field="amount" label="Amount" />
                </flux:chart.tooltip>
            </flux:chart>
        </div>
        <flux:card class="overflow-hidden min-w-[12rem]">
            <flux:text class="mb-3">Categories</flux:text>
            @foreach ($this->current->groupBy('category')->map->sum('amount')->sortDesc() as $category => $sum)
                <div class="flex space-between gap-8">
                    <span class="flex-1 text-sm">{{ $category }}</span>
                    <span class="text-sm tabular-nums">${{ number_format($sum / 100) }}</span>
                </div>
            @endforeach
        </flux:card>
    </div>

    <div class="flex items-center space-x-6 mb-8">
        <flux:heading size="xl">Expenses</flux:heading>
        <flux:modal.trigger name="expense-form">
            <flux:button icon="plus">New</flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Results --}}
    <flux:table :paginate="$expenses">
        <flux:table.columns>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column align="right">Amount</flux:table.column>
            <flux:table.column align="center">Category</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Method</flux:table.column>
            <flux:table.column>Notes</flux:table.column>
            <flux:table.column>Created At</flux:table.column>
            <flux:table.column />
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($expenses as $expense)
                <flux:table.row>
                    <flux:table.cell>{{ $expense->date->toDateString() }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->description }}</flux:table.cell>
                    <flux:table.cell class="text-right text-base!">${{ number_format($expense->amount / 100) }}</flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge variant="pill" :color="$expense->categoryBadgeColor()">{{ $expense->category }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="pill">{{ $expense->type }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="pill">{{ $expense->payment_method }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($expense->notes)
                            <flux:tooltip :content="$expense->notes">
                                <flux:icon.chat-bubble-left-ellipsis class="text-amber-400" />
                            </flux:tooltip>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $expense->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" variant="ghost" icon="pencil" icon-variant="outline" wire:click="edit({{ $expense->id }}); Flux.modal('expense-form').show()" />
                        <flux:button size="sm" variant="ghost" icon="trash" icon-variant="outline" wire:click="delete({{ $expense->id }})" wire:confirm="Are you sure?" icon:class="text-red-400" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="expense-form" class="w-md" wire:close="clear">
        <div class="space-y-8">
            <flux:heading size="xl" class="text-zinc-300">
                @if ($editing)
                    Edit Expense #{{ $editing->id }}
                @else
                    New Expense
                @endif
            </flux:heading>
            <flux:date-picker label="Date" wire:model="date" max="today" />
            <flux:input label="Description" wire:model="description" />
            <flux:input label="Amount" mask:dynamic="$money($input)" icon="currency-dollar" icon-variant="outline" wire:model="amount" />
            <flux:select label="Category" placeholder="Choose..." wire:model="category">
                @foreach (self::categories() as $category)
                    <flux:select.option>{{ $category }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select label="Type" placeholder="Choose..." wire:model="type">
                @foreach (self::types() as $type)
                    <flux:select.option>{{ $type }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select label="Payment Method" placeholder="Choose..." wire:model="paymentMethod">
                @foreach (self::paymentMethods() as $paymentMethod)
                    <flux:select.option>{{ $paymentMethod }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input autofocus label="External ID" wire:model="externalId" badge="optional" />
            <flux:textarea
                label="Notes"
                badge="optional"
                placeholder="No lettuce, tomato, or onion..."
                wire:model="notes"
            />
            <div class="text-right space-x-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="save">
                    {{ $this->editing ? 'Update' : 'Submit' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
