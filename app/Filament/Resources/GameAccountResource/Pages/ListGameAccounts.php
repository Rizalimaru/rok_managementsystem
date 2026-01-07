<?php

namespace App\Filament\Resources\GameAccountResource\Pages;

use App\Filament\Resources\GameAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameAccounts extends ListRecords
{
    protected static string $resource = GameAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
