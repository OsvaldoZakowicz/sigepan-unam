<?php

namespace App\Mail;

use App\Models\PreOrderPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CantClosePreOrderPeriod extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   * @param PreOrderPeriod $preorder_period periodo de pre ordenes.
   */
  public function __construct(public PreOrderPeriod $preorder_period) {}

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Un periodo de pre ordenes no pudo cerrarse!',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.cant-close-pre-order-period',
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
