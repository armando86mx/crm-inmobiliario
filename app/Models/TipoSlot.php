<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSlot extends Model
{
    use HasFactory;

    /**
     * El "guardia de seguridad" para el guardado.
     * Debe estar en minúscula para coincidir con la base de datos.
     */
    protected $fillable = [
        'nombre',
    ];
}