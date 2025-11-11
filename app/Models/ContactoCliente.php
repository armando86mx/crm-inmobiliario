<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactoCliente extends Model
{
    use HasFactory;

    protected $table = 'contactos_cliente';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'puesto',
        'email',
        'telefono',
        'es_principal',
        'orden',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Boot del modelo con lógica especial
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear un contacto
        static::creating(function ($contacto) {
            // Si es el primer contacto del cliente, marcarlo como principal
            $contactosExistentes = static::where('cliente_id', $contacto->cliente_id)->count();

            if ($contactosExistentes === 0) {
                $contacto->es_principal = true;
            }

            // Auto-ordenar: asignar el siguiente número de orden
            if (empty($contacto->orden)) {
                $maxOrden = static::where('cliente_id', $contacto->cliente_id)->max('orden') ?? 0;
                $contacto->orden = $maxOrden + 1;
            }
        });

        // Al actualizar un contacto
        static::updating(function ($contacto) {
            // Si se marca como principal, desmarcar los demás del mismo cliente
            if ($contacto->isDirty('es_principal') && $contacto->es_principal) {
                static::where('cliente_id', $contacto->cliente_id)
                    ->where('id', '!=', $contacto->id)
                    ->update(['es_principal' => false]);
            }
        });

        // Al guardar (crear o actualizar)
        static::saved(function ($contacto) {
            // Asegurar que siempre haya un contacto principal
            $tienePrincipal = static::where('cliente_id', $contacto->cliente_id)
                ->where('es_principal', true)
                ->exists();

            if (!$tienePrincipal) {
                // Si no hay principal, marcar el primero como principal
                $primerContacto = static::where('cliente_id', $contacto->cliente_id)
                    ->orderBy('orden')
                    ->first();

                if ($primerContacto) {
                    $primerContacto->es_principal = true;
                    $primerContacto->saveQuietly(); // Evitar recursión
                }
            }
        });

        // Al eliminar un contacto
        static::deleting(function ($contacto) {
            // No permitir eliminar si es el único contacto
            $totalContactos = static::where('cliente_id', $contacto->cliente_id)->count();

            if ($totalContactos <= 1) {
                throw new \Exception('No se puede eliminar el único contacto del cliente.');
            }

            // Si se elimina el contacto principal, asignar otro como principal
            if ($contacto->es_principal) {
                $nuevoContactoPrincipal = static::where('cliente_id', $contacto->cliente_id)
                    ->where('id', '!=', $contacto->id)
                    ->orderBy('orden')
                    ->first();

                if ($nuevoContactoPrincipal) {
                    $nuevoContactoPrincipal->es_principal = true;
                    $nuevoContactoPrincipal->saveQuietly(); // Evitar recursión
                }
            }
        });
    }

    /**
     * Relación con Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
