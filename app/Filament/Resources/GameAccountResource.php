<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameAccountResource\Pages;
use App\Filament\Resources\GameAccountResource\RelationManagers;
use App\Models\GameAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontFamily;

class GameAccountResource extends Resource
{
    protected static ?string $model = GameAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Section::make('Credentials')
                        ->description('Informasi login ke Rise of Kingdoms.')
                        ->schema([
                            TextInput::make('username')
                                ->label('Email / Username')
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-user'), // Ikon user

                            TextInput::make('password')
                                ->password() // Mode password
                                ->revealable() // Fitur tombol "mata" untuk melihat password
                                ->required() // Atau nullable jika login via Google tanpa pass
                                ->prefixIcon('heroicon-m-key'),
                            
                            Select::make('login_method')
                                ->options([
                                    'email' => 'Email / Lilith Account',
                                    'google' => 'Google Account',
                                    'facebook' => 'Facebook',
                                    'apple' => 'Apple ID',
                                ])
                                ->default('email')
                                ->required()
                                ->native(false), // Tampilan dropdown lebih modern
                        ])->columns(2),

                    Section::make('Status & Catatan')
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'active' => 'Active (Aman)',
                                    'maintenance' => 'Maintenance (Rawat Jalan)',
                                    'banned' => 'Banned (Terblokir)',
                                ])
                                ->default('active')
                                ->required()
                                ->native(false),

                            Textarea::make('notes')
                                ->label('Catatan Khusus')
                                ->placeholder('Misal: Akun ini kena limit transfer sampai tanggal sekian...')
                                ->rows(3)
                                ->columnSpanFull(), // Lebar penuh
                        ])->columns(2),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Kolom Username/Email (Dipercantik)
                TextColumn::make('username')
                    ->label('Username / Email')
                    ->searchable()
                    ->copyable() 
                    ->copyMessage('Email disalin!')
                    ->icon('heroicon-m-envelope') // Ikon Amplop
                    ->weight('bold')
                    ->color('primary'),

                // 2. Kolom Password (BARU)
                TextColumn::make('password')
                    ->label('Password')
                    ->copyable()
                    ->copyMessage('Password disalin!')
                    ->icon('heroicon-m-key') // Ikon Kunci
                    ->fontFamily(FontFamily::Mono) // Font koding biar jelas terbaca
                    ->color('gray') // Warna abu biar tidak terlalu mencolok visualnya
                    // Opsional: Jika ingin password sensor bintang-bintang tapi tetap bisa dicopy:
                    // ->formatStateUsing(fn () => '••••••••') 
                    // ->copyableState(fn ($state) => $state)
                    ,

                TextColumn::make('login_method')
                    ->badge() 
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'maintenance',
                        'danger' => 'banned',
                    ])
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-m-check-circle',
                        'maintenance' => 'heroicon-m-wrench-screwdriver',
                        'banned' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),
                    
                TextColumn::make('characters_count')
                    ->counts('characters') 
                    ->label('Jml Karakter')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true), 

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'banned' => 'Banned',
                    ]),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGameAccounts::route('/'),
            'create' => Pages\CreateGameAccount::route('/create'),
            'edit' => Pages\EditGameAccount::route('/{record}/edit'),
        ];
    }
}
