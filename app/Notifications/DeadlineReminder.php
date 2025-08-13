<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Deadline;

class DeadlineReminder extends Notification
{
    use Queueable;

    protected $deadline;

    /**
     * Create a new notification instance.
     */
    public function __construct(Deadline $deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * Get the notification\'s delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/tenant/deadlines/{$this->deadline->id}/edit");

        return (new MailMessage)
                    ->subject("Lembrete de Prazo: {$this->deadline->title}")
                    ->greeting("Olá!")
                    ->line("Este é um lembrete para o prazo: **{$this->deadline->title}**.")
                    ->line("A data de vencimento é: **{$this->deadline->due_date->format("d/m/Y")}**.")
                    ->action("Ver Prazo", $url)
                    ->line("Não perca este prazo importante!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "deadline_id" => $this->deadline->id,
            "title" => $this->deadline->title,
            "due_date" => $this->deadline->due_date->format("Y-m-d"),
        ];
    }
}
