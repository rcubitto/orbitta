<div class="flex gap-20">
    <div class="w-1/3 2xl:w-1/6 space-y-6">
        <flux:field>
            <flux:label>Date</flux:label>
            <flux:date-picker wire:model="date" />
            <flux:error name="date" />
        </flux:field>

        <flux:input label="Description" wire:model="description" />

        <flux:input label="Amount" mask:dynamic="$money($input)" icon="currency-dollar" wire:model="amount" />

        <flux:select label="Category" placeholder="Choose..." wire:model="category">
            <flux:select.option>Dogs</flux:select.option>
            <flux:select.option>Drugs</flux:select.option>
            <flux:select.option>Entertainment</flux:select.option>
            <flux:select.option>Extras</flux:select.option>
            <flux:select.option>Groceries</flux:select.option>
            <flux:select.option>Healthcare</flux:select.option>
            <flux:select.option>Household</flux:select.option>
            <flux:select.option>Housing</flux:select.option>
            <flux:select.option>Online Services</flux:select.option>
            <flux:select.option>Other</flux:select.option>
            <flux:select.option>Taxes & Accounting</flux:select.option>
            <flux:select.option>Transport</flux:select.option>
            <flux:select.option>Utilities</flux:select.option>
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
            <flux:card class="overflow-hidden min-w-[12rem]">
                <flux:text>Total {{ today()->format('M, Y') }}</flux:text>
                <flux:heading size="xl" class="mt-2 tabular-nums">
                    ${{
                        number_format(\App\Models\Expense::query()
                            ->whereMonth('date', today()->month)
                            ->whereYear('date', today()->year)
                            ->sum('amount') / 100)
                    }}
                </flux:heading>
            </flux:card>
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
