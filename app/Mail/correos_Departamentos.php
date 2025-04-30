<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class correos_Departamentos extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;

    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Pruebas de Correos Deptos')
            ->view('emails.departamentos.base') // este se ocupa para crear el diseño del correo
            ->attachData($this->pdf->output(), 'listado_departamentos.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}