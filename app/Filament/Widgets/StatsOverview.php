<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\GameAccount;
use App\Models\Character;
use Illuminate\Support\Number; // Helper Laravel

class StatsOverview extends BaseWidget
{
    // Mengatur agar widget ini refresh otomatis setiap 15 detik (Live data)
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Hitung Omset Bulan Ini (Hanya dari order yang Completed)
        $income = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');

        // 2. Hitung Order yang butuh perhatian (Pending/Processing)
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->count();

        // 3. Hitung Total Akun Aktif
        $activeAccounts = GameAccount::where('status', 'active')->count();

        return [
            Stat::make('Omset Bulan Ini', 'Rp ' . number_format($income, 0, ',', '.'))
                ->description('Total order selesai bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Dummy chart visual
                ->color('success'),

            Stat::make('Order Perlu Proses', $pendingOrders)
                ->description('Pending & Processing')
                ->descriptionIcon('heroicon-m-bell')
                ->color($pendingOrders > 0 ? 'danger' : 'success'), // Merah jika ada order

            Stat::make('Farm Accounts Aktif', $activeAccounts)
                ->description('Siap digunakan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}