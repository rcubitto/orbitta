<div class="space-y-6">
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

    <flux:textarea
        label="Notes"
        placeholder="No lettuce, tomato, or onion..."
        wire:model="notes"
    />

    <flux:button variant="primary" wire:click="save">
        Submit
    </flux:button>
</div>
