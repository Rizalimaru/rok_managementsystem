<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\GameAccount;
use Illuminate\Support\Facades\Cache; // Wajib Import ini

class StatsOverview extends BaseWidget
{
    // HAPUS atau set null agar tidak auto-refresh (bikin berat di localhost)
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // 1. Hitung Omset (Cache 5 Menit)
        $income = Cache::remember('stats_income', 300, function () {
            return Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('total_price');
        });

        // 2. Hitung Pending Order (Cache 1 Menit - biar agak realtime dikit)
        $pendingOrders = Cache::remember('stats_pending', 60, function () {
            return Order::whereIn('status', ['pending', 'processing'])->count();
        });

        // 3. Hitung Akun Aktif (Cache 5 Menit)
        $activeAccounts = Cache::remember('stats_active_accounts', 300, function () {
            return GameAccount::where('status', 'active')->count();
        });

        return [
            Stat::make('Omset Bulan Ini', 'Rp ' . number_format($income, 0, ',', '.'))
                ->description('Total order selesai bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17]) 
                ->color('success'),

            Stat::make('Order Perlu Proses', $pendingOrders)
                ->description('Pending & Processing')
                ->descriptionIcon('heroicon-m-bell')
                ->color($pendingOrders > 0 ? 'danger' : 'success'),

            Stat::make('Farm Accounts Aktif', $activeAccounts)
                ->description('Siap digunakan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}