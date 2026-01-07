<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CharacterResource\Pages;
use App\Models\Character;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class CharacterResource extends Resource
{
    protected static ?string $model = Character::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Karakter')
                    ->description('Data identitas karakter game.')
                    ->schema([
                        Select::make('game_account_id')
                            ->relationship('gameAccount', 'username')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Akun Login'),

                        Select::make('kingdom_id')
                            ->relationship('kingdom', 'server_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Server/Kingdom'),

                        TextInput::make('ign')
                            ->required()
                            ->label('In-Game Name (IGN)'),

                        TextInput::make('governor_id')
                            ->numeric()
                            ->label('Governor ID (Angka)'),
                    ])->columns(2),

                // REVISI BAGIAN INI: Input Satuan M
                Section::make('Inventory Resource')
                    ->description('Masukkan stok dalam satuan M (Juta). Contoh: Ketik 150 untuk 150.000.000')
                    ->schema([
                        TextInput::make('food')
                            ->label('Food')
                            ->numeric()
                            ->default(0)
                            ->suffix('M') // Penanda Satuan
                            ->required()
                            // Load: Bagi 1 Juta (DB -> Form)
                            ->formatStateUsing(fn ($state) => $state ? $state / 1000000 : null)
                            // Save: Kali 1 Juta (Form -> DB)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 1000000)),

                        TextInput::make('wood')
                            ->label('Wood')
                            ->numeric()
                            ->default(0)
                            ->suffix('M')
                            ->required()
                            ->formatStateUsing(fn ($state) => $state ? $state / 1000000 : null)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 1000000)),

                        TextInput::make('stone')
                            ->label('Stone')
                            ->numeric()
                            ->default(0)
                            ->suffix('M')
                            ->required()
                            ->formatStateUsing(fn ($state) => $state ? $state / 1000000 : null)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 1000000)),

                        TextInput::make('gold')
                            ->label('Gold')
                            ->numeric()
                            ->default(0)
                            ->suffix('M')
                            ->required()
                            ->formatStateUsing(fn ($state) => $state ? $state / 1000000 : null)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 1000000)),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kingdom.server_number')
                    ->label('Server')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ign')
                    ->label('IGN')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('gameAccount.username')
                    ->label('Login Akun')
                    ->limit(20)
                    ->color('gray'),

                // Tampilan Tabel tetap menggunakan format M (sudah benar dari revisi sebelumnya)
                TextColumn::make('food')
                    ->label('Food')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 2) . ' M'),

                TextColumn::make('wood')
                    ->label('Wood')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 2) . ' M'),
                
                TextColumn::make('stone')
                    ->label('Stone')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 2) . ' M'), 

                TextColumn::make('gold')
                    ->label('Gold')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 2) . ' M')
                    ->color('warning'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('kingdom_id')
                    ->relationship('kingdom', 'server_number')
                    ->label('Filter Server'),
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
            'index' => Pages\ListCharacters::route('/'),
            'create' => Pages\CreateCharacter::route('/create'),
            'edit' => Pages\EditCharacter::route('/{record}/edit'),
        ];
    }
}