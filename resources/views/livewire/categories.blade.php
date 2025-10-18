<div>
    <div class="flex items-center mb-8">
        <flux:heading size="xl">Categories</flux:heading>
        <flux:separator vertical class="mx-4"/>
        <flux:modal.trigger name="category-form">
            <flux:button icon="plus">New</flux:button>
        </flux:modal.trigger>
    </div>

    <div>
        <ul class="grid grid-flow-col gap-8">
            @foreach ($categories as $category)
                <li>
                    <flux:card>
                        <flux:heading size="lg">{{ $category->name }}</flux:heading>
                        @if ($category->children->isNotEmpty())
                        <ul class="mt-2 space-y-1">
                            @foreach ($category->children as $subcategory)
                                <li class="flex items-center space-x-2 hover:bg-zinc-600">
                                    <flux:icon.arrow-right class="size-3 text-zinc-400" />
                                    <input type="text" wire:model.live.debounce="stateCategories.{{ $subcategory->id }}" class="text-white/70 text-sm" />
                                </li>
                            @endforeach
                        </ul>
                    </flux:card>
                </li>
                @endif
            @endforeach
        </ul>
    </div>

    <flux:modal name="category-form" class="w-md" wire:close="clear">
        <div class="space-y-8">
            <flux:heading size="xl" class="text-zinc-300">
                @if ($editing)
                    Edit Category {{ $editing->name }}
                @else
                    New Category
                @endif
            </flux:heading>
            <flux:input label="Name" wire:model="name" />
            <flux:select label="Parent" placeholder="Choose..." wire:model="parent">
                <flux:select.option value="">Root</flux:select.option>
                @foreach ($this->rootCategories as $category)
                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
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
