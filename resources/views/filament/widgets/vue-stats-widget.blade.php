<x-filament-widgets::widget>
    <x-filament::section>
        <div id="vue-stats" 
            data-user="{{ json_encode(['name' => auth()->user()->name, 'role' => auth()->user()->role]) }}"
        >
            <!-- Vue will mount here -->
        </div>
    </x-filament::section>

</x-filament-widgets::widget>
