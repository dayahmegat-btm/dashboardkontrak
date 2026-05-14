<?php

namespace App\Filament\Resources\JenisBonResource\Pages;

use App\Filament\Resources\JenisBonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisBon extends EditRecord
{
    protected static string $resource = JenisBonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
