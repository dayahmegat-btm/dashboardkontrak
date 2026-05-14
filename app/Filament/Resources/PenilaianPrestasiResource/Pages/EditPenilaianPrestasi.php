<?php

namespace App\Filament\Resources\PenilaianPrestasiResource\Pages;

use App\Filament\Resources\PenilaianPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianPrestasi extends EditRecord
{
    protected static string $resource = PenilaianPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
