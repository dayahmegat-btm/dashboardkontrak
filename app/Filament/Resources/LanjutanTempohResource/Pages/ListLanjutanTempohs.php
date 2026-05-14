<?php

namespace App\Filament\Resources\LanjutanTempohResource\Pages;

use App\Filament\Resources\LanjutanTempohResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanjutanTempohs extends ListRecords
{
    protected static string $resource = LanjutanTempohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
