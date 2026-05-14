<?php

namespace App\Filament\Resources\PembekalResource\Pages;

use App\Filament\Resources\PembekalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembekals extends ListRecords
{
    protected static string $resource = PembekalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
