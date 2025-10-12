<div class="flex gap-20">
    <div class="w-1/3 2xl:w-1/6 space-y-6">
        <flux:input label="External ID" wire:model="externalId" />

        <flux:field>
            <flux:label>Date</flux:label>
            <flux:date-picker wire:model="date" max="today" />
        </flux:field>

        <flux:input label="Description" wire:model="description" />

        <flux:input label="Amount" mask:dynamic="$money($input)" icon="currency-dollar" wire:model="amount" />

        <flux:select label="Category" placeholder="Choose..." wire:model="category">
            @foreach ($this->categories as $category)
                <flux:select.option>{{ $category }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select label="Type" placeholder="Choose..." wire:model="type">
            <flux:select.option>Recurring (Fixed)</flux:select.option>
            <flux:select.option>Recurring (Variable)</flux:select.option>
            <flux:select.option>One-Time</flux:select.option>
            <flux:select.option>Installment</flux:select.option>
        </flux:select>

        <flux:select label="Payment Method" placeholder="Choose..." wire:model="paymentMethod">
            <flux:select.option>VISA</flux:select.option>
            <flux:select.option>Master</flux:select.option>
            <flux:select.option>MP</flux:select.option>
            <flux:select.option>Cash</flux:select.option>
            <flux:select.option>Bank Transfer</flux:select.option>
            <flux:select.option>Auto Debit</flux:select.option>
            <flux:select.option>PagoMisCuentas</flux:select.option>
            <flux:select.option>PayPal</flux:select.option>
        </flux:select>

        <div class="col-span-2">
            <flux:textarea
                label="Notes"
                placeholder="No lettuce, tomato, or onion..."
                wire:model="notes"
            />
        </div>

        <flux:button variant="primary" wire:click="save">
            Submit
        </flux:button>
    </div>
    <div class="flex-1">
        <div class="mb-16 flex gap-10">
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
        <flux:table :paginate="$expenses">
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Description</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Method</flux:table.column>
                <flux:table.column>Notes</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($expenses as $expense)
                    <flux:table.row>
                        <flux:table.cell>{{ $expense->date->toDateString() }}</flux:table.cell>
                        <flux:table.cell>{{ $expense->description }}</flux:table.cell>
                        <flux:table.cell class="text-right" variant="strong">${{ number_format($expense->amount / 100) }}</flux:table.cell>
                        <flux:table.cell>
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
                                    <flux:icon.chat-bubble-left-ellipsis class="text-amber-500" />
                                </flux:tooltip>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $expense->created_at->diffForHumans() }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
