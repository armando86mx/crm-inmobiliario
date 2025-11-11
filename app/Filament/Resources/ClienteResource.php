<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $pluralLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Datos Fiscales
                Forms\Components\TextInput::make('razon_social')
                    ->label('Razón Social')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nombre_comercial')
                    ->label('Nombre Comercial')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('rfc')
                    ->label('RFC')
                    ->required()
                    ->maxLength(13)
                    ->unique(ignoreRecord: true)
                    ->regex('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/')
                    ->validationMessages([
                        'regex' => 'El formato del RFC no es válido.',
                    ])
                    ->columnSpan(1),

                Forms\Components\TextInput::make('regimen_fiscal')
                    ->label('Régimen Fiscal')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(1),

                Forms\Components\Select::make('uso_cfdi')
                    ->label('Uso CFDI')
                    ->required()
                    ->options([
                        'G01' => 'G01 - Adquisición de mercancías',
                        'G03' => 'G03 - Gastos en general',
                        'P01' => 'P01 - Por definir',
                    ])
                    ->columnSpan(1),

                Forms\Components\TextInput::make('email_facturacion')
                    ->label('Email de Facturación')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                // Contactos
                Forms\Components\Repeater::make('contactos')
                    ->label('Contactos del Cliente')
                    ->relationship('contactos')
                    ->schema([
                        Forms\Components\Checkbox::make('es_principal')
                            ->label('Es Principal')
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('puesto')
                            ->label('Puesto')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->minItems(1)
                    ->reorderable(true)
                    ->orderColumn('orden')
                    ->addActionLabel('Añadir Contacto')
                    ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null)
                    ->collapsible()
                    ->columnSpanFull(),

                // Estado y Contrato
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'prospecto' => 'Prospecto',
                        'trial' => 'Trial',
                        'activo' => 'Activo',
                        'pausado' => 'Pausado',
                        'vencido' => 'Vencido',
                        'suspendido' => 'Suspendido',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('prospecto')
                    ->reactive()
                    ->columnSpan(1),

                Forms\Components\DateTimePicker::make('trial_ends_at')
                    ->label('Fin de Trial')
                    ->visible(fn (Forms\Get $get) => $get('estado') === 'trial')
                    ->timezone('America/Mexico_City')
                    ->columnSpan(1),

                Forms\Components\Select::make('frecuencia')
                    ->label('Frecuencia de Pago')
                    ->options([
                        'mensual' => 'Mensual',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                    ])
                    ->disabled(fn (Forms\Get $get) => $get('estado') === 'prospecto')
                    ->columnSpan(1),

                Forms\Components\Select::make('dia_ciclo')
                    ->label('Día de Ciclo de Facturación')
                    ->options(array_combine(range(1, 31), range(1, 31)))
                    ->disabled(fn (Forms\Get $get) => $get('estado') === 'prospecto')
                    ->columnSpan(1),

                Forms\Components\DateTimePicker::make('fecha_activacion')
                    ->label('Fecha de Activación')
                    ->disabled()
                    ->visible(fn (Forms\Get $get) => $get('estado') !== 'prospecto')
                    ->timezone('America/Mexico_City')
                    ->columnSpan(1),

                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('razon_social')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('nombre_comercial')
                    ->label('Nombre Comercial')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('RFC copiado')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospecto' => 'gray',
                        'trial' => 'warning',
                        'activo' => 'success',
                        'pausado' => 'info',
                        'vencido' => 'danger',
                        'suspendido' => 'danger',
                        'cancelado' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('contactoPrincipal.nombre')
                    ->label('Contacto Principal')
                    ->getStateUsing(fn (Cliente $record) => $record->contacto_principal?->nombre ?? '-')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('contactos', function ($q) use ($search) {
                            $q->where('nombre', 'like', "%{$search}%")
                              ->where('es_principal', true);
                        });
                    }),

                Tables\Columns\TextColumn::make('contactoPrincipal.email')
                    ->label('Email de Contacto')
                    ->getStateUsing(fn (Cliente $record) => $record->contacto_principal?->email ?? '-')
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('contactos', function ($q) use ($search) {
                            $q->where('email', 'like', "%{$search}%")
                              ->where('es_principal', true);
                        });
                    }),

                // COMENTADO TEMPORALMENTE - El modelo Slot aún no existe
                // Tables\Columns\TextColumn::make('slots_count')
                //     ->label('Slots')
                //     ->counts('slots')
                //     ->default(0)
                //     ->alignCenter(),

                Tables\Columns\TextColumn::make('proxima_facturacion')
                    ->label('Próxima Facturación')
                    ->getStateUsing(function (Cliente $record) {
                        if (!$record->proxima_facturacion) {
                            return '-';
                        }
                        try {
                            return \Carbon\Carbon::parse($record->proxima_facturacion)->format('d/M/Y');
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->multiple()
                    ->options([
                        'prospecto' => 'Prospecto',
                        'trial' => 'Trial',
                        'activo' => 'Activo',
                        'pausado' => 'Pausado',
                        'vencido' => 'Vencido',
                        'suspendido' => 'Suspendido',
                        'cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('frecuencia')
                    ->label('Frecuencia')
                    ->options([
                        'mensual' => 'Mensual',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
