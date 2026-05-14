<?php

namespace App\Filament\Resources\PembekalResource\Pages;

use App\Filament\Resources\PembekalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembekal extends EditRecord
{
    protected static string $resource = PembekalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
