<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Ringkasan Resource
        </x-slot>

        <div class="flex flex-col gap-4 mt-2">
            {{-- Food --}}
            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-white/10">
                <span class="flex items-center gap-2 font-medium text-gray-600 dark:text-gray-300">🌾 Food</span>
                <span class="font-bold text-lg text-green-600 dark:text-green-400">{{ number_format($resources['food'], 2) }} M</span>
            </div>
            
            {{-- Wood --}}
            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-white/10">
                <span class="flex items-center gap-2 font-medium text-gray-600 dark:text-gray-300">🪵 Wood</span>
                <span class="font-bold text-lg text-yellow-600 dark:text-yellow-400">{{ number_format($resources['wood'], 2) }} M</span>
            </div>

            {{-- Stone --}}
            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-white/10">
                <span class="flex items-center gap-2 font-medium text-gray-600 dark:text-gray-300">🪨 Stone</span>
                <span class="font-bold text-lg text-gray-500 dark:text-gray-400">{{ number_format($resources['stone'], 2) }} M</span>
            </div>

            {{-- Gold --}}
            <div class="flex justify-between items-center">
                <span class="flex items-center gap-2 font-medium text-gray-600 dark:text-gray-300">🪙 Gold</span>
                <span class="font-bold text-lg text-amber-500 dark:text-amber-400">{{ number_format($resources['gold'], 2) }} M</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>