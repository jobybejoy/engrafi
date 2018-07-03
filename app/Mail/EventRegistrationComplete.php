<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventRegistrationComplete extends Mailable
{
    use Queueable, SerializesModels;

    public $event_name;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event_name,$date)
    {
        $this->event_name = $event_name;
        $this->date       = $date;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.EventRegistrationComplete');
    }
}
