<?php

namespace App\Filament\Resources\LanjutanTempohResource\Pages;

use App\Filament\Resources\LanjutanTempohResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLanjutanTempoh extends ViewRecord
{
    protected static string $resource = LanjutanTempohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
