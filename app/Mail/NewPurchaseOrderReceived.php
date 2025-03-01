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

class NewPurchaseOrderReceived extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   * @param Supplier $supplier
   * @param $PreOrder $preorder
   */
  public function __construct(
    public Supplier $supplier,
    public PreOrder $preorder
  ) {}

  /**
   * Get the message envelope.
   * @return Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: '¡Nueva orden de compra recibida!',
    );
  }

  /**
   * Get the message content definition.
   * todo: falta mostrar datos de la orden
   * @return Content
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.new-purchase-order-received',
    );
  }

  /**
   * Get the attachments for the message.
   * todo: adjuntar PDF orden y albaran
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
