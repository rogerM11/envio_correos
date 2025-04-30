<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Departamento;
use Illuminate\Support\Facades\Mail;
use App\Mail\correos_Departamentos;
use Barryvdh\DomPDF\Facade\Pdf;
class EnviarReporteDepartamentos extends Command
{
    /**
     * El nombre y firma del comando.
     */
    protected $signature = 'app:enviar-reporte-departamentos';

    /**
     * Descripción del comando.
     */
    protected $description = 'Envia un correo de prueba con PDF sobre departamentos';

    public function handle()
    {
        $departamento = new \stdClass();
        $departamento->codigo = '001';
        $departamento->Alias = 'La Libertad';
        $departamento->Correo = 'enriquelopez@labcofasa.com';
        $departamento->Telefono = '1234567890';

        $pdf = Pdf::loadView('pdfsEnviar.departamento', compact('departamento'));
        Mail::to($departamento->Correo)->send(new correos_Departamentos($pdf));

        $this->info("Correo enviado a {$departamento->Correo}");
    }
}
