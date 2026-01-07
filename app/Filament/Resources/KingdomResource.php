<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KingdomResource\Pages;
use App\Models\Kingdom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class KingdomResource extends Resource
{
    protected static ?string $model = Kingdom::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt'; // Icon server

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Server')
                    ->description('Nomor server dan nama kingdom.')
                    ->schema([
                        TextInput::make('server_number')
                            ->label('Nomor Server (KD)')
                            ->placeholder('Contoh: 1920')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('name')
                            ->label('Nama Kingdom (Opsional)')
                            ->placeholder('Contoh: Kingdom of Suro')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Rate Harga Jual (Per 1M Resource)')
                    ->description('Masukkan harga jual dalam Rupiah untuk setiap 1.000.000 unit resource.')
                    ->schema([
                        // KOREKSI: Label diperjelas agar Admin tidak bingung
                        TextInput::make('price_food')
                            ->label('Harga Food (Per 1M)')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('price_wood')
                            ->label('Harga Wood (Per 1M)')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('price_stone')
                            ->label('Harga Stone (Per 1M)')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('price_gold')
                            ->label('Harga Gold (Per 1M)')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('server_number')
                    ->label('Server (KD)')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Nama Kingdom')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('price_food')
                    ->label('Food / 1M')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('price_wood')
                    ->label('Wood / 1M')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('price_stone')
                    ->label('Stone / 1M')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('price_gold')
                    ->label('Gold / 1M')
                    ->money('IDR')
                    ->sortable()
                    ->color('warning'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKingdoms::route('/'),
            'create' => Pages\CreateKingdom::route('/create'),
            'edit' => Pages\EditKingdom::route('/{record}/edit'),
        ];
    }
}