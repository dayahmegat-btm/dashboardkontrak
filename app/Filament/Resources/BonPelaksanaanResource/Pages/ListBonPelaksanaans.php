<?php

namespace App\Filament\Resources\BonPelaksanaanResource\Pages;

use App\Filament\Resources\BonPelaksanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBonPelaksanaans extends ListRecords
{
    protected static string $resource = BonPelaksanaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
