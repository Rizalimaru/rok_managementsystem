<?php

namespace App\Filament\Resources\GameAccountResource\Pages;

use App\Filament\Resources\GameAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameAccount extends EditRecord
{
    protected static string $resource = GameAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
