<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $subjectLine;
    public string $bodyText;

    public function __construct(string $name, string $email, string $subjectLine, string $bodyText)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subjectLine = $subjectLine ?: 'Pesan Baru dari Form Kontak';
        $this->bodyText = $bodyText;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->markdown('emails.contact');
    }
}
