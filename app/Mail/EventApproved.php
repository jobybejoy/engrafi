<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $event_name , $event_date , $event_category ;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event_name,$event_date,$event_category)
    {
        $this->event_name = $event_name;
        $this->event_date = $event_date;
        $this->event_category = $event_category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.EventApproved');
    }
}
