<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
class correos_vendedores extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfCofasa;
    public $pdfNemul;
    public $excel;
   
    public function __construct($pdfCofasa = null, $pdfNemul = null,$excel = null)
    {
        $this->pdfCofasa = $pdfCofasa;
        $this->pdfNemul = $pdfNemul;
        $this->excel = $excel;
    }

    /**
     * Construye el mensaje.
     */
    public function build()
    {
        $email = $this->subject('Saldos Pendientes de cobro')
                      ->view('emails.vendedores.base');

        if ($this->pdfCofasa) {
            $email->attachData($this->pdfCofasa->output(), 'Cofasa_facturas_vivas.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        if ($this->pdfNemul) {
            $email->attachData($this->pdfNemul->output(), 'Nemul_facturas_vivas.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        if ($this->excel) {
            $email->attachData($this->excel, 'Facturas_vivas.xlsx', [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
        
        return $email;
    }
}
