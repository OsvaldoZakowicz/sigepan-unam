<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
  use Queueable;

  /**
   * El destinatario del correo electrónico.
   * @var string
   */
  protected $recipient;

  /**
   * La instancia de Mailable.
   * correo a enviar.
   * @var Mailable
   */
  protected Mailable $mailable;

  /**
   * Numero de intentos máximos
  */
  public $tries = 3;

  /**
   * crear una nueva instancia de trabajo.
   * @return void
   */
  public function __construct(string $recipient, Mailable $mailable)
  {
    $this->recipient = $recipient;
    $this->mailable = $mailable;
  }

  /**
   * Ejecutar el trabajo.
   * @return void
   */
  public function handle(): void
  {
    Mail::to($this->recipient)->send($this->mailable);
  }
}
