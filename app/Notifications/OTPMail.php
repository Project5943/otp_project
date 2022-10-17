<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class OTPMail extends Mailable
{
    use Queueable,SerializesModels;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $data = $this->toArray(); 

        $messages = $this->from(env('MAIL_FROM'),env('MAIL_FROM_NAME'))
                    ->subject($data['subject'])
                    ->view($data['view'])
                    ->with($data); 
    
        
        return $messages;         
    }

    public function toArray()
    {
        $data = $this->data;
        return $data;
    }
}
