<?php

namespace App\Filament\Resources\DaftarSstResource\Pages;

use App\Filament\Resources\DaftarSstResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarSsts extends ListRecords
{
    protected static string $resource = DaftarSstResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
