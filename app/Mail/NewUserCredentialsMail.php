<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $loginUrl;

    public function __construct($user, string $plainPassword, ?string $loginUrl = null)
    {
        $this->user          = $user;
        $this->plainPassword = $plainPassword;
        $this->loginUrl      = $loginUrl ?: url('/login');
    }

    public function build()
    {
        return $this->subject('Benvenuto in Pasticcere Pro – Le tue credenziali')
            ->view('emails.users.credentials')
            ->with([
                'name'      => $this->user->name,
                'email'     => $this->user->email,
                'password'  => $this->plainPassword,
                'loginUrl'  => $this->loginUrl,
                'support'   => config('mail.from.address'),
                'appUrl'    => config('app.url'),
            ]);
    }
}
