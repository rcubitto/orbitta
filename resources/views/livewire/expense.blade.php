<div>
    <div class="flex items-center mb-8">
        <flux:heading size="xl">Expenses</flux:heading>
        <flux:separator vertical class="mx-4"/>
        <flux:modal.trigger name="expense-form">
            <flux:button icon="plus">New</flux:button>
        </flux:modal.trigger>
        <flux:select variant="listbox" class="w-auto! ml-4" wire:model.live="dateRangePreset">
            <x-slot name="trigger">
                <flux:select.button>
                    <flux:icon.funnel variant="micro" class="mr-2 text-zinc-400" />
                    <flux:select.selected />
                </flux:select.button>
            </x-slot>
            @foreach(\Flux\DateRangePreset::cases() as $preset)
                <flux:select.option value="{{ $preset->value }}">{{ $preset->label() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select variant="listbox" multiple wire:model.live="filteredCategories" placeholder="Categories" class="w-auto! ml-4" clearable>
            @foreach(\App\Models\Category::whereNotNull('parent_id')->pluck('name', 'id') as $id => $name)
                <flux:select.option value="{{ $id }}">{{ $name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select variant="listbox" multiple wire:model.live="filteredTypes" placeholder="Types" class="w-auto! ml-4" clearable>
            @foreach(self::types() as $type)
                <flux:select.option>{{ $type }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Results --}}
    <flux:table :paginate="$expenses">
        <flux:table.columns>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Date</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>Amount</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Method</flux:table.column>
            <flux:table.column>Notes</flux:table.column>
            <flux:table.column>Created At</flux:table.column>
            <flux:table.column />
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($expenses as $expense)
                <flux:table.row :key="$expense->id">
                    <flux:table.cell>
                        <span class="text-zinc-500">#</span>
                        <span>{{ $expense->id }}</span>
                    </flux:table.cell>
                    <flux:table.cell>{{ $expense->date->format('M j, Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $expense->description }}</flux:table.cell>
                    <flux:table.cell variant="strong">${{ number_format($expense->amount / 100) }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$expense->categoryBadgeColor()">{{ $expense->category->name }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge>{{ $expense->type }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge>{{ $expense->payment_method }}</flux:badge>
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
            <flux:select label="Category" placeholder="Choose..." wire:model="categoryId">
                @foreach ($this->categories as $rootCategory)
                    <optgroup label="{{ $rootCategory->name }}">
                        @foreach ($rootCategory->children as $subcategory)
                            <flux:select.option :value="$subcategory->id">{{ $subcategory->name }}</flux:select.option>
                        @endforeach
                    </optgroup>
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
            <flux:textarea
                label="Notes"
                badge="optional"
                placeholder="No lettuce, tomato, or onion..."
                wire:model="notes"
            />
            @unless ($editing)
            <flux:field variant="inline">
                <flux:checkbox wire:model="keepAdding" />
                <flux:label>Keep adding expenses</flux:label>
            </flux:field>
            @endunless
            <div class="text-right space-x-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="save()">
                    {{ $this->editing ? 'Update' : 'Submit' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
