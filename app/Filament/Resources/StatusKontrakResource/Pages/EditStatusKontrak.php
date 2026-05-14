<?php

namespace App\Filament\Resources\StatusKontrakResource\Pages;

use App\Filament\Resources\StatusKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatusKontrak extends EditRecord
{
    protected static string $resource = StatusKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
