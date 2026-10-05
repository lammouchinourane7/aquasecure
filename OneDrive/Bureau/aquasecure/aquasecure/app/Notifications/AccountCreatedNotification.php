<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $plainPassword)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $roleLabel = match ($notifiable->role) {
            'admin' => 'administrateur',
            'gestionnaire' => 'gestionnaire',
            default => 'citoyen',
        };

        return (new MailMessage)
            ->subject('Votre compte AquaSecure a été créé')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Un compte AquaSecure vient d'être créé pour vous par un administrateur, avec le rôle {$roleLabel}.")
            ->line('Voici vos identifiants de connexion :')
            ->line('Email : '.$notifiable->email)
            ->line('Mot de passe temporaire : '.$this->plainPassword)
            ->action('Se connecter', route('login'))
            ->line('Nous vous recommandons de changer ce mot de passe après votre première connexion.');
    }
}
