<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountActivation extends Mailable
{
    use Queueable, SerializesModels;


    public $name;
    public $confirmation_code;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$confirmation_code)
    {
        $this->name = $name;
        $this->confirmation_code=$confirmation_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.AccountActivation');
    }
}
