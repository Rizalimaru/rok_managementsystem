<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Character;

class ResourceChart extends ChartWidget
{
    protected static ?string $heading = 'Global Inventory (Semua Akun)';
    
    // Urutan tampilan di dashboard (biar di bawah kartu stats)
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        // Hitung total semua resource dari seluruh karakter
        // Kita bagi 1.000.000 agar angkanya enak dilihat (Satuan M)
        $totalFood = Character::sum('food') / 1000000;
        $totalWood = Character::sum('wood') / 1000000;
        $totalStone = Character::sum('stone') / 1000000;
        $totalGold = Character::sum('gold') / 1000000;

        return [
            'datasets' => [
                [
                    'label' => 'Stok Tersedia (Juta / M)',
                    'data' => [$totalFood, $totalWood, $totalStone, $totalGold],
                    'backgroundColor' => [
                        '#22c55e', // Hijau (Food)
                        '#eab308', // Kuning/Kayu (Wood)
                        '#64748b', // Abu (Stone)
                        '#f59e0b', // Emas (Gold)
                    ],
                ],
            ],
            'labels' => ['Food', 'Wood', 'Stone', 'Gold'],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa ganti 'doughnut', 'line', atau 'pie'
    }
}