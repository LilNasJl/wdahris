<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">{{ $this->getEmployeeName() }}</x-slot>

        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div>
                <x-filament::button type="submit">
                    Save Changes
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-panels::page>
