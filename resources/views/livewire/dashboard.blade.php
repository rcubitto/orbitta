<div>
    <div class="flex items-center mb-8">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:separator vertical class="mx-4"/>
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
    </div>

    <div class="grid grid-cols-6 gap-10 mb-8">
        <flux:card class="overflow-hidden min-w-[12rem]">
            <flux:text>Count</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ $count }}</flux:heading>
        </flux:card>
        <flux:card class="overflow-hidden min-w-[12rem]">
            <flux:text>Total</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ money($total) }}</flux:heading>
        </flux:card>
        <flux:card class="overflow-hidden min-w-[12rem]">
            <flux:text>Sum By Type</flux:text>
            @foreach ($sumByType as $stat)
                <flux:text>{{ $stat->type }} - {{ money($stat->total) }} - {{ $total ? round($stat->total / $total * 100) : 0 }}%</flux:text>
            @endforeach
        </flux:card>
    </div>

    <div class="grid grid-cols-5 gap-8">
        @foreach ($byCategory as $parentCategory => $stats)
        <flux:card class="overflow-hidden min-w-[12rem]">
            <flux:heading>{{ $parentCategory }} - {{ money($totalParent = $stats->sum('expenses_sum_amount')) }}</flux:heading>
            @foreach ($stats->sortByDesc('expenses_sum_amount') as $stat)
                <div class="flex justify-between">
                    <flux:text>{{ $stat->name }}</flux:text>
                    <flux:text class="tabular-nums">
                       {{ $totalParent ? round($stat->expenses_sum_amount / $totalParent * 100) : 0 }}% - {{ money($stat->expenses_sum_amount) }}
                    </flux:text>
                </div>
            @endforeach
        </flux:card>
        @endforeach
    </div>
</div>
