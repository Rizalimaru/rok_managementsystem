<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;

class LatestOrders extends BaseWidget
{
    protected static ?string $heading = 'Orderan Terbaru (5 Transaksi Terakhir)';
    
    // Urutan ke-3 (Paling bawah)
    protected static ?int $sort = 3;

    // Agar tabel ini memanjang penuh (full width) dari kiri ke kanan
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil 5 order terakhir dari database
                Order::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M H:i') // Format: 06 Jan 14:30
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('kingdom.server_number')
                    ->label('Server (KD)'),

                // Menampilkan Total Items (Contoh: 500 M)
                Tables\Columns\TextColumn::make('items_sum_amount')
                    ->sum('items', 'amount')
                    ->label('Total Request')
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 0) . ' M')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tagihan')
                    ->money('IDR')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // Tombol pintas untuk langsung buka detail/edit order
                Tables\Actions\Action::make('open')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->label('Proses')
                    ->button()
                    ->color('primary'),
            ]);
    }
}