<?php

namespace App\Filament\Resources\KaedahPerolehanResource\Pages;

use App\Filament\Resources\KaedahPerolehanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKaedahPerolehans extends ListRecords
{
    protected static string $resource = KaedahPerolehanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
