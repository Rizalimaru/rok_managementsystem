<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Kingdom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource\RelationManagers;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info Pelanggan & Server')
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('whatsapp'),
                            ]),

                        Select::make('kingdom_id')
                            ->relationship('kingdom', 'server_number')
                            ->label('Target Server')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateGrandTotal($get, $set);
                            }),
                    ])->columns(2),

                // REVISI: Fixed Repeater dengan Icon & Tampilan Grid
                Section::make('Keranjang Belanja')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            // Kunci agar tidak bisa tambah/hapus manual
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            // Tampilkan 4 Item Default secara otomatis saat Create Baru
                            ->default([
                                ['resource_type' => 'food', 'amount' => 0],
                                ['resource_type' => 'wood', 'amount' => 0],
                                ['resource_type' => 'stone', 'amount' => 0],
                                ['resource_type' => 'gold', 'amount' => 0],
                            ])
                            ->schema([
                                // Hidden field untuk menyimpan tipe (Food/Wood/dll)
                                Hidden::make('resource_type'),

                                TextInput::make('amount')
                                    // Label Dinamis: "Food", "Wood", dll
                                    ->label(fn (Get $get) => ucfirst($get('resource_type')))
                                    
                                    // Icon Dinamis: Berubah sesuai tipe resource
                                    ->prefix(fn (Get $get) => new HtmlString(
                                            // Sesuaikan path '/images/resource_icons/' dengan folder Anda
                                            '<img src="/images/resource_icons/' . $get('resource_type') . '.png" 
                                                style="width: 20px; height: 20px; object-fit: contain;" 
                                                alt="' . $get('resource_type') . '" />'
                                        ))
                                    // Warna Icon Dinamis
                                    ->prefixIconColor(fn (Get $get) => match ($get('resource_type')) {
                                        'food' => 'success', // Hijau
                                        'wood' => 'warning', // Coklat/Kuning tua
                                        'stone' => 'gray',   // Abu
                                        'gold' => 'warning', // Kuning Emas
                                        default => 'gray',
                                    })
                                    
                                    ->placeholder('0')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('M')
                                    ->required()
                                    
                                    // Konversi Satuan M (Tetap sama seperti sebelumnya)
                                    ->formatStateUsing(fn ($state) => $state ? $state / 1000000 : 0)
                                    ->dehydrateStateUsing(fn ($state) => (int)($state * 1000000))
                                    
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        self::updateLineItem($get, $set);
                                        self::updateGrandTotal($get, $set);
                                    }),

                                Hidden::make('subtotal_price'),
                            ])
                            ->grid(2) // Tampil 2 kolom (Food-Wood di atas, Stone-Gold di bawah)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pembayaran')
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Total Tagihan (IDR)')
                            ->prefix('Rp')
                            ->numeric()
                            ->readOnly()
                            ->default(0),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),

                        DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Kirim'),

                        Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function updateLineItem(Get $get, Set $set): void
    {
        // Ambil ID Kingdom dari parent form (naik 3 level karena ada di dalam Repeater -> Grid -> Section)
        $kingdomId = $get('../../kingdom_id'); 
        
        if (!$kingdomId) return;
        
        $kingdom = Kingdom::find($kingdomId);
        if (!$kingdom) return;

        $type = $get('resource_type');
        $amountInM = (float) $get('amount'); 

        $pricePer1M = match ($type) {
            'food' => $kingdom->price_food,
            'wood' => $kingdom->price_wood,
            'stone' => $kingdom->price_stone,
            'gold' => $kingdom->price_gold,
            default => 0,
        };

        $subtotal = $amountInM * $pricePer1M;
        $set('subtotal_price', $subtotal);
    }

    public static function updateGrandTotal(Get $get, Set $set): void
    {
        // Ambil data items. Karena dipanggil dari berbagai tempat, kita coba cari path yang benar
        $items = $get('../../items') ?? $get('items') ?? [];
        $kingdomId = $get('../../kingdom_id') ?? $get('kingdom_id');

        if (!$kingdomId) return;

        $kingdom = Kingdom::find($kingdomId);
        if (!$kingdom) return;

        $grandTotal = 0;

        foreach ($items as $item) {
            $type = $item['resource_type'] ?? null;
            $amountInM = (float) ($item['amount'] ?? 0);

            if ($type && $amountInM > 0) {
                $pricePer1M = match ($type) {
                    'food' => $kingdom->price_food,
                    'wood' => $kingdom->price_wood,
                    'stone' => $kingdom->price_stone,
                    'gold' => $kingdom->price_gold,
                    default => 0,
                };
                
                $grandTotal += ($amountInM * $pricePer1M);
            }
        }

        // Set total_price di form utama
        // Kita coba set di beberapa kemungkinan path agar aman
        $set('../../total_price', $grandTotal);
        $set('total_price', $grandTotal);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->prefix('#')->sortable(),
                TextColumn::make('customer.name')->searchable(),
                TextColumn::make('kingdom.server_number')->label('Server'),
                
                TextColumn::make('items_sum_amount')
                    ->sum('items', 'amount')
                    ->label('Total RSS')
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 0) . ' M') 
                    ->sortable(),

                TextColumn::make('total_price')
                    ->money('IDR')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}