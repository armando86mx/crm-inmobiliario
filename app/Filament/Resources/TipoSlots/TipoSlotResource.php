<?php

namespace App\Filament\Resources\TipoSlots;

use App\Filament\Resources\TipoSlots\Pages;
use App\Models\TipoSlot;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Schemas\Schema; // <-- La clave de V4

class TipoSlotResource extends Resource
{
    protected static ?string $model = TipoSlot::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombre';

    // ¡¡AQUÍ ESTÁ EL FORMULARIO!!
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    // ¡¡AQUÍ ESTÁ LA TABLA!!
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoSlots::route('/'),
            'create' => Pages\CreateTipoSlot::route('/create'),
            'edit' => Pages\EditTipoSlot::route('/{record}/edit'),
        ];
    }
}