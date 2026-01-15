<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="h-5 w-5 text-warning-500" />
                Acciones Rápidas
            </div>
        </x-slot>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($this->getActions() as $action)
                <a 
                    href="{{ $action['url'] }}" 
                    class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-{{ $action['color'] }}-500 hover:bg-{{ $action['color'] }}-50 dark:hover:bg-{{ $action['color'] }}-900/20 transition-all duration-200"
                >
                    <div class="p-3 rounded-full bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/30 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400 mb-2 group-hover:scale-110 transition-transform">
                        @php
                            $iconClass = $action['icon'];
                        @endphp
                        <x-dynamic-component :component="$iconClass" class="h-6 w-6" />
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
                        {{ $action['label'] }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">
                        {{ $action['description'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
