<?php

namespace App\Filament\Resources\DaftarSstResource\Pages;

use App\Filament\Resources\DaftarSstResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarSst extends EditRecord
{
    protected static string $resource = DaftarSstResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
