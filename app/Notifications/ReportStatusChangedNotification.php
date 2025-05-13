<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Report;

class ReportStatusChangedNotification extends Notification
{
    use Queueable;

    public string $newStatus;
    public Report $report;

    public function __construct(string $newStatus, Report $report)
    {
        $this->newStatus = $newStatus;
        $this->report = $report;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mise à jour de votre signalement')
            ->greeting('Bonjour ' . $notifiable->use_username . ',')
            ->line("Le statut de votre signalement a été mis à jour : **{$this->newStatus}**")
            ->line("Message : {$this->report->rep_message}")
            ->line('Merci pour votre contribution.')
            ->salutation('L’équipe de maintenance');
    }
}
