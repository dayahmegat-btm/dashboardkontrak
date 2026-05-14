<?php

namespace App\Filament\Resources\BonPelaksanaanResource\Pages;

use App\Filament\Resources\BonPelaksanaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBonPelaksanaan extends EditRecord
{
    protected static string $resource = BonPelaksanaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
