<x-filament-panels::page>
    <div id="admin-dashboard" 
         data-user="{{ json_encode(['name' => auth()->user()->name, 'role' => auth()->user()->role]) }}">
    </div>
</x-filament-panels::page>
