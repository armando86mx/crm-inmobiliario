<?php

namespace App\Filament\Resources\TipoSlots\Pages;

use App\Filament\Resources\TipoSlots\TipoSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoSlots extends ListRecords
{
    protected static string $resource = TipoSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
