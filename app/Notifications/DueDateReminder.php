<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DueDateReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $customerName,
        public string $filmTitle,
        public \DateTimeInterface $dueDate,
        public bool $overdue
    ) {}

    public function via($notifiable): array
    { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $when = $this->dueDate->format('Y-m-d');
        return (new MailMessage)
            ->subject($this->overdue ? '☝️ Renta vencida' : '⏰ Próxima devolución')
            ->greeting('Hola '.$this->customerName)
            ->line('Película: '.$this->filmTitle)
            ->line('Fecha límite: '.$when)
            ->line($this->overdue ? 'Tu renta está vencida. Devuélvela cuanto antes.' : 'Faltan pocos días para la devolución.')
            ->line('Gracias por usar nuestro servicio.');
    }
}
