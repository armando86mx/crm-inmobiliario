<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'uuid',
        'razon_social',
        'nombre_comercial',
        'rfc',
        'uso_cfdi',
        'regimen_fiscal',
        'email_facturacion',
        'estado',
        'trial_ends_at',
        'dia_ciclo',
        'frecuencia',
        'fecha_activacion',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'fecha_activacion' => 'datetime',
        'dia_ciclo' => 'integer',
    ];

    protected $appends = [
        'monto_renovacion',
        'proxima_facturacion',
        'contacto_principal',
    ];

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generar UUID al crear
        static::creating(function ($cliente) {
            if (empty($cliente->uuid)) {
                $cliente->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relación con ContactoCliente
     */
    public function contactos(): HasMany
    {
        return $this->hasMany(ContactoCliente::class, 'cliente_id')
            ->orderBy('orden');
    }

    /**
     * Relación con Slot (preparado para futuro)
     * COMENTADO TEMPORALMENTE - El modelo Slot aún no existe
     */
    // public function slots(): HasMany
    // {
    //     return $this->hasMany(Slot::class, 'cliente_id');
    // }

    /**
     * Usuario que creó el registro
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó el registro
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Clientes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Clientes vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }

    /**
     * Scope: Clientes prospectos
     */
    public function scopeProspectos($query)
    {
        return $query->where('estado', 'prospecto');
    }

    /**
     * Computed property: Monto de renovación
     * Calcula el precio según slots actuales
     * TEMPORALMENTE retorna 0 hasta que se implemente el modelo Slot
     */
    public function getMontoRenovacionAttribute(): float
    {
        // Por ahora retorna 0, se implementará cuando exista el modelo Slot
        return 0.0;

        // CÓDIGO COMENTADO TEMPORALMENTE - El modelo Slot aún no existe
        // $slotsBasicas = $this->slots()->where('tipo', 'basica')->count();
        // $slotsDestacadas = $this->slots()->where('tipo', 'destacada')->count();
        //
        // // Precios ejemplo (ajustar según tu lógica de negocio)
        // $precioBasica = 100;
        // $precioDestacada = 200;
        //
        // return ($slotsBasicas * $precioBasica) + ($slotsDestacadas * $precioDestacada);
    }

    /**
     * Computed property: Próxima fecha de facturación
     * Calcula fecha según dia_ciclo
     */
    public function getProximaFacturacionAttribute(): ?string
    {
        if ($this->estado === 'prospecto' || !$this->dia_ciclo || !$this->fecha_activacion) {
            return null;
        }

        $now = now();
        $year = $now->year;
        $month = $now->month;

        // Si el día del ciclo ya pasó este mes, calcular para el próximo mes
        if ($now->day >= $this->dia_ciclo) {
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        // Ajustar si el día no existe en el mes (ej: 31 en febrero)
        $lastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $day = min($this->dia_ciclo, $lastDayOfMonth);

        try {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Computed property: Contacto principal
     * Retorna el contacto marcado como principal
     */
    public function getContactoPrincipalAttribute(): ?ContactoCliente
    {
        return $this->contactos()->where('es_principal', true)->first();
    }

    /**
     * Método: Activar contrato
     * Activa el contrato del cliente con los parámetros especificados
     */
    public function activarContrato(
        int $slotsBasicas,
        int $slotsDestacadas,
        string $frecuencia,
        int $diaCiclo
    ): bool {
        $this->update([
            'estado' => 'activo',
            'frecuencia' => $frecuencia,
            'dia_ciclo' => $diaCiclo,
            'fecha_activacion' => now(),
        ]);

        // Aquí se crearían los slots cuando el modelo Slot esté implementado
        // Por ahora solo actualizamos el estado del cliente

        return true;
    }
}
