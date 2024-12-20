<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEmailToDoctorAfterRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $code;
    public $email;

    public function __construct($codeToSend, $sendToemail)
    {
        $this ->code = $codeToSend;
        $this ->email = $sendToemail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Creation de compte docteur')
                    ->line('Votre compte a été créer avec succès sur la plate-forme.')
                    ->line('Cliquez sur le bouton ci-dessous pour valider votre compte')
                    ->line('Saisissez le code '.$this->code.' et renseignez le dans le formulaire qui apparaitra ')
                    ->action('Cliquez ici', url('/validate-account' . '/' .$this->email))
                    ->line('Merci d\'utiliser notre application');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
