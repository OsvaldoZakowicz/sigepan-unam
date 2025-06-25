<?php

namespace App\Mail;

use App\Models\DatoNegocio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class InternalUserRegistered extends Mailable
{
  use Queueable, SerializesModels;

  public array $datos_negocio;

  public function __construct(
    public User $user,
    public $password,
  ) {
    $this->datos_negocio = DatoNegocio::obtenerTodos();
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Usuario Interno Registrado',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.internal-user-registered',
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
