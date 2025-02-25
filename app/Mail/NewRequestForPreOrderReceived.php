<?php

namespace App\Mail;

use App\Models\PreOrder;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRequestForPreOrderReceived extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   * @param Supplier $supplier
   * @param PreOrder $pre_order
   */
  public function __construct(
    public Supplier $supplier,
    public PreOrder $pre_order
  ) {}

  /**
   * asunto del correo
   * @return Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: '¡Nueva solicitud de pre orden de compra recibida!',
    );
  }

  /**
   * cuerpo del correo
   * @return Content
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.new-request-for-pre-order-received',
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
