<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters; // Import trait
use App\Models\Character;
use Illuminate\Support\Facades\Cache;

class ResourceListWidget extends Widget
{
    use InteractsWithPageFilters; // Aktifkan trait

    protected static string $view = 'filament.widgets.resource-list-widget';
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 1; 

    protected function getViewData(): array
    {
        // Tangkap nilai KD dari filter global
        $activeFilter = $this->filters['kingdom_id'] ?? 'all';
        $cacheKey = 'list_resource_' . $activeFilter;

        $data = Cache::remember($cacheKey, 600, function () use ($activeFilter) {
            $query = Character::query();

            if ($activeFilter !== 'all') {
                $query->where('kingdom_id', $activeFilter);
            }

            return [
                'food' => $query->sum('food') / 1000000,
                'wood' => $query->sum('wood') / 1000000,
                'stone' => $query->sum('stone') / 1000000,
                'gold' => $query->sum('gold') / 1000000,
            ];
        });

        return [
            'resources' => $data,
        ];
    }
}