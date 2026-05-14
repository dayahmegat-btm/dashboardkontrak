<?php

namespace App\Filament\Resources\KategoriPerkhidmatanResource\Pages;

use App\Filament\Resources\KategoriPerkhidmatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPerkhidmatan extends EditRecord
{
    protected static string $resource = KategoriPerkhidmatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
