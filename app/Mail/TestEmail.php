<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data; // Variabel yang akan dikirim ke view
    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function build()
    {
        return $this->to($this->data['email'])// Kirim ke email tujuan
                    ->subject($this->data['subject'])// Subjek email
                    ->view('testmail')// View email
                    ->with('data', $this->data); // Kirim data ke view
    }
}
