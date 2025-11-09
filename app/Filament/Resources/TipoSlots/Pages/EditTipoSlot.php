<?php

namespace App\Filament\Resources\TipoSlots\Pages;

use App\Filament\Resources\TipoSlots\TipoSlotResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Necesario para el método de abajo

class EditTipoSlot extends EditRecord
{
    protected static string $resource = TipoSlotResource::class;

    // ESTE MÉTODO V4 REEMPLAZA EL COMPORTAMIENTO DE GUARDADO
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }

    // ESTE MÉTODO V4 FUERZA LA REDIRECCIÓN a la tabla 'index' después de guardar
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    // NOTA: No existe el método getHeaderActions, por lo que Filament usa su botón por defecto (el de abajo).
}