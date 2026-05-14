<?php

namespace App\Filament\Resources\DaftarKontrakResource\Pages;

use App\Filament\Resources\DaftarKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarKontraks extends ListRecords
{
    protected static string $resource = DaftarKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
