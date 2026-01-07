<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Character;
use Illuminate\Support\Facades\Cache; // Wajib Import ini

class ResourceChart extends ChartWidget
{
    protected static ?string $heading = 'Global Inventory (Semua Akun)';
    protected static ?int $sort = 2; 
    
    // Matikan polling refresh otomatis
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Cache data chart selama 10 menit (600 detik)
        // Karena stok resource global tidak perlu realtime detik-an.
        $data = Cache::remember('chart_resource_global', 600, function () {
            return [
                'food' => Character::sum('food') / 1000000,
                'wood' => Character::sum('wood') / 1000000,
                'stone' => Character::sum('stone') / 1000000,
                'gold' => Character::sum('gold') / 1000000,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Stok Tersedia (Juta / M)',
                    // Ambil data dari variabel cache
                    'data' => [$data['food'], $data['wood'], $data['stone'], $data['gold']],
                    'backgroundColor' => [
                        '#22c55e', // Hijau
                        '#eab308', // Kuning
                        '#64748b', // Abu
                        '#f59e0b', // Emas
                    ],
                ],
            ],
            'labels' => ['Food', 'Wood', 'Stone', 'Gold'],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; 
    }
}