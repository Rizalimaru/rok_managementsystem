<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Models\Kingdom;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm; 

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kingdom_id')
                    ->label('Filter Server (KD)')
                    ->options(
                        ['all' => 'Semua Akun (Global)'] + Kingdom::pluck('server_number', 'id')->toArray()
                    )
                    ->default('all')
                    ->searchable(),
            ])
            ->columns(3); 
    }
}