<?php

namespace App\Filament\Resources\TipoSlots\Pages;

use App\Filament\Resources\TipoSlots\TipoSlotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoSlot extends CreateRecord
{
    protected static string $resource = TipoSlotResource::class;

    // ¡¡AÑADE ESTE BLOQUE!!
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}