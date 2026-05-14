<?php

namespace App\Filament\Resources\SeksyenUnitResource\Pages;

use App\Filament\Resources\SeksyenUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeksyenUnits extends ListRecords
{
    protected static string $resource = SeksyenUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
