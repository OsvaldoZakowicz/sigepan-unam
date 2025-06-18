<?php

namespace App\Mail;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CloseQuotationPeriod extends Mailable
{
  use Queueable, SerializesModels;

  // cantidad de presupuestos respondidos
  public $quotations_count = 0;

  /**
   * Create a new message instance.
   */
  public function __construct(public RequestForQuotationPeriod $quotation_period)
  {
    $qps = new QuotationPeriodService();
    $this->quotations_count = $qps->countQuotationsFromPeriod($quotation_period->id);
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Un periodo de presupuestos ha sido cerrado con éxito!',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.close-quotation-period',
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
