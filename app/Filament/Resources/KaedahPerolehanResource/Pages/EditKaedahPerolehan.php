<?php

namespace App\Filament\Resources\KaedahPerolehanResource\Pages;

use App\Filament\Resources\KaedahPerolehanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKaedahPerolehan extends EditRecord
{
    protected static string $resource = KaedahPerolehanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
