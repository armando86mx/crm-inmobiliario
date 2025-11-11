<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\ContactoCliente;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cliente 1: Prospecto con 2 contactos
        $cliente1 = Cliente::create([
            'razon_social' => 'DESARROLLADORA ABC SA DE CV',
            'nombre_comercial' => 'Desarrolladora ABC',
            'rfc' => 'DABC910101ABC',
            'uso_cfdi' => 'G03',
            'regimen_fiscal' => '601 - General de Ley Personas Morales',
            'email_facturacion' => 'facturacion@desarrolladoraabc.com',
            'estado' => 'prospecto',
            'observaciones' => 'Prospecto interesado en 10 slots básicas para su nuevo desarrollo residencial.',
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente1->id,
            'nombre' => 'Juan Carlos Pérez',
            'puesto' => 'Director General',
            'email' => 'jcarlos.perez@desarrolladoraabc.com',
            'telefono' => '5555551234',
            'es_principal' => true,
            'orden' => 1,
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente1->id,
            'nombre' => 'María Fernanda González',
            'puesto' => 'Gerente de Ventas',
            'email' => 'mfernanda.gonzalez@desarrolladoraabc.com',
            'telefono' => '5555555678',
            'es_principal' => false,
            'orden' => 2,
        ]);

        // Cliente 2: Activo con 3 contactos
        $cliente2 = Cliente::create([
            'razon_social' => 'INMOBILIARIA XYZ SC',
            'nombre_comercial' => 'Inmobiliaria XYZ',
            'rfc' => 'IXY850215XYZ',
            'uso_cfdi' => 'G01',
            'regimen_fiscal' => '601 - General de Ley Personas Morales',
            'email_facturacion' => 'contabilidad@inmobiliariaxyz.com',
            'estado' => 'activo',
            'dia_ciclo' => 15,
            'frecuencia' => 'mensual',
            'fecha_activacion' => Carbon::now()->subMonths(6),
            'observaciones' => 'Cliente activo con 15 slots básicas y 5 destacadas. Excelente historial de pagos.',
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente2->id,
            'nombre' => 'Roberto Martínez Sánchez',
            'puesto' => 'Director Comercial',
            'email' => 'roberto.martinez@inmobiliariaxyz.com',
            'telefono' => '5555559876',
            'es_principal' => true,
            'orden' => 1,
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente2->id,
            'nombre' => 'Laura Patricia Hernández',
            'puesto' => 'Gerente de Marketing',
            'email' => 'laura.hernandez@inmobiliariaxyz.com',
            'telefono' => '5555554321',
            'es_principal' => false,
            'orden' => 2,
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente2->id,
            'nombre' => 'Carlos Eduardo López',
            'puesto' => 'Coordinador de Operaciones',
            'email' => 'carlos.lopez@inmobiliariaxyz.com',
            'telefono' => '5555558765',
            'es_principal' => false,
            'orden' => 3,
        ]);

        // Cliente 3: Trial con 1 contacto
        $cliente3 = Cliente::create([
            'razon_social' => 'CONSTRUCTORA DEL VALLE SA',
            'nombre_comercial' => 'Constructora del Valle',
            'rfc' => 'CDV920530VAL',
            'uso_cfdi' => 'G03',
            'regimen_fiscal' => '601 - General de Ley Personas Morales',
            'email_facturacion' => 'admin@constructoradelvalle.com',
            'estado' => 'trial',
            'trial_ends_at' => Carbon::now()->addDays(7),
            'observaciones' => 'En periodo de prueba. Interesados en paquete semestral.',
        ]);

        ContactoCliente::create([
            'cliente_id' => $cliente3->id,
            'nombre' => 'Ana Sofía Ramírez',
            'puesto' => 'Gerente General',
            'email' => 'ana.ramirez@constructoradelvalle.com',
            'telefono' => '5555552468',
            'es_principal' => true,
            'orden' => 1,
        ]);

        $this->command->info('✓ Se crearon 3 clientes con sus contactos exitosamente');
    }
}
