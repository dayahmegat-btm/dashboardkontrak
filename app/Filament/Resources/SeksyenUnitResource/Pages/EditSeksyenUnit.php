<?php

namespace App\Filament\Resources\SeksyenUnitResource\Pages;

use App\Filament\Resources\SeksyenUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeksyenUnit extends EditRecord
{
    protected static string $resource = SeksyenUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
