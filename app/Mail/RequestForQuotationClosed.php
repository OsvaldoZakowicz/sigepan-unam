<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\DatoNegocio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestForQuotationClosed extends Mailable
{
  use Queueable, SerializesModels;

  public Supplier $supplier;
  public Quotation $quotation;
  public array $datos_negocio;

  public function __construct(Supplier $supplier, Quotation $quotation)
  {
    $this->supplier = $supplier;
    $this->quotation = $quotation;
    $this->datos_negocio = DatoNegocio::obtenerTodos();
  }

  /**
   * asunto del correo
   * @return Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: '¡Solicitud de presupuestos cerrada!',
    );
  }

  /**
   * cuerpo del correo
   * @return Content
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.request-for-quotation-closed',
    );
  }

  /**
   * Get the attachments for the message.
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
