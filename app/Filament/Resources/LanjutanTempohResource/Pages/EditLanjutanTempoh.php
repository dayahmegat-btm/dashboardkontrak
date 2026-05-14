<?php

namespace App\Filament\Resources\LanjutanTempohResource\Pages;

use App\Filament\Resources\LanjutanTempohResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLanjutanTempoh extends EditRecord
{
    protected static string $resource = LanjutanTempohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
