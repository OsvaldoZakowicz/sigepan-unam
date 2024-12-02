<?php

namespace App\Mail;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * * email: proveedor registrado
 * enviar al proveedor sus credenciales de acceso al sistema.
 */
class SupplierRegistered extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(public User $user, public $password, public Supplier $supplier) {}

  /**
   * * encabezado del correo
   * uso el from global de config\mail.php
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Proveedor registrado',
    );
  }

  /**
   * * cuerpo del correo
   * el cuerpo sera una vista blade.php estatica
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.supplier-registered',
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
