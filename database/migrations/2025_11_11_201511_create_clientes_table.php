<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255);
            $table->string('rfc', 13)->unique();
            $table->string('uso_cfdi', 50);
            $table->string('regimen_fiscal', 100);
            $table->string('email_facturacion', 255);
            $table->enum('estado', [
                'prospecto',
                'trial',
                'activo',
                'pausado',
                'vencido',
                'suspendido',
                'cancelado'
            ])->default('prospecto');
            $table->timestamp('trial_ends_at')->nullable();
            $table->integer('dia_ciclo')->nullable()->check('dia_ciclo >= 1 AND dia_ciclo <= 31');
            $table->enum('frecuencia', ['mensual', 'semestral', 'anual'])->nullable();
            $table->timestamp('fecha_activacion')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
