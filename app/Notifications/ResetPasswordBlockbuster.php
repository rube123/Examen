<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordBlockbuster extends Notification
{
    use Queueable;

    /** @var string */
    public string $url;

    /**
     * Crea la notificación con la URL ya armada.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Canales de entrega.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Contenido del correo.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recupera tu contraseña')
            ->greeting('🎬 Rentas for Movies')
            ->line('Haz clic en el botón para restablecer tu contraseña.')
            ->action('Restablecer contraseña', $this->url)
            ->line('Si no solicitaste esto, ignora este correo.');
    }

    /**
     * (Opcional) Representación en array.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
