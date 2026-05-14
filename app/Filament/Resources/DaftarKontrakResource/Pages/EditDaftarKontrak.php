<?php

namespace App\Filament\Resources\DaftarKontrakResource\Pages;

use App\Filament\Resources\DaftarKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarKontrak extends EditRecord
{
    protected static string $resource = DaftarKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
