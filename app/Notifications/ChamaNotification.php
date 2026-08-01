<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ChamaNotification extends Notification
{
    use Queueable;


    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null
    )
    {

    }



    public function via($notifiable)
    {
        return [
            'database',
            'mail'
        ];
    }



    public function toDatabase($notifiable)
    {
        return [

            'title'=>$this->title,

            'message'=>$this->message,

            'url'=>$this->url

        ];
    }



    public function toMail($notifiable)
    {
        return (new MailMessage)

            ->subject($this->title)

            ->greeting(
                'Hello '.$notifiable->name
            )

            ->line($this->message)

            ->when(
                $this->url,
                function($mail){
                    return $mail->action(
                        'Open ChamaYetu',
                        $this->url
                    );
                }
            );
    }
}