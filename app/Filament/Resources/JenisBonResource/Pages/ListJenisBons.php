<?php

namespace App\Filament\Resources\JenisBonResource\Pages;

use App\Filament\Resources\JenisBonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisBons extends ListRecords
{
    protected static string $resource = JenisBonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
