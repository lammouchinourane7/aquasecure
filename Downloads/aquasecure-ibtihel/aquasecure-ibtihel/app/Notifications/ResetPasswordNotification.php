<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->greeting('Bonjour !')
            ->line('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte AquaSecure.')
            ->action('Réinitialiser le mot de passe', $url)
            ->line("Ce lien de réinitialisation expirera dans {$minutes} minutes.")
            ->line("Si vous n'êtes pas à l'origine de cette demande, aucune action n'est requise.");
    }
}
