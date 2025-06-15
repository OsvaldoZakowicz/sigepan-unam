<?php

namespace App\Mail;

use App\Models\PreOrder;
use App\Models\Supplier;
use App\Models\DatoNegocio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestForPreOrderClosed extends Mailable
{
  use Queueable, SerializesModels;

  public Supplier $supplier;
  public PreOrder $preorder;
  public array $datos_negocio;

  /**
   * Create a new message instance.
   */
  public function __construct(Supplier $supplier, PreOrder $preorder)
  {
    $this->supplier = $supplier;
    $this->preorder = $preorder;
    $this->datos_negocio = DatoNegocio::obtenerTodos();
  }

  /**
   * Get the message envelope.
   * @return Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: '¡Solicitud de pre ordenes de compra cerrada!',
    );
  }

  /**
   * Get the message content definition.
   * @return Content
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.request-for-pre-order-closed',
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
