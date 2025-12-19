<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationTokenNotification extends Notification
{
    use Queueable;

    /**
     * The verification token.
     *
     * @var string
     */
    public $token;

    /**
     * The user's name.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @param  string  $name
     * @return void
     */
    public function __construct($token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Kode Verifikasi Pendaftaran - Barber Booking')
            ->greeting('Halo ' . $this->name . '!')
            ->line('Terima kasih telah mendaftar di Barber Booking.')
            ->line('Berikut adalah kode verifikasi Anda:')
            ->line('**' . $this->token . '**')
            ->line('Kode ini akan kadaluarsa dalam 30 menit.')
            ->line('Jika Anda tidak mendaftar di Barber Booking, abaikan email ini.')
            ->salutation('Salam, Tim Barber Booking');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
