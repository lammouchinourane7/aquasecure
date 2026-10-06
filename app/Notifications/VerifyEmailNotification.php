<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmailNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirmez votre adresse e-mail')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Merci de vous être inscrit sur AquaSecure ! Confirmez votre adresse e-mail en cliquant sur le bouton ci-dessous.')
            ->action('Confirmer mon e-mail', $url)
            ->line("Si vous n'êtes pas à l'origine de cette inscription, aucune action n'est requise.");
    }
}
