<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailInvoiceNotif extends Mailable
{
    use Queueable, SerializesModels;
    public $data; // Variabel yang akan dikirim ke view
    public $pdfPath;
    /**
     * Create a new message instance.
     */
    public function __construct($data, $pdfPath = "")
    {
        $this->data = $data;
        if($pdfPath != ""){
            $this->pdfPath = $pdfPath;
        }
    }
    public function build()
    {
        return $this->to($this->data['email'])// Kirim ke email tujuan
                    ->subject($this->data['subject'])// Subjek email
                    ->view($this->data['view'])// View email
                    ->attach($this->pdfPath, ['as' => "Invoice_{$this->data['no_invoice']}.pdf",'mime' => 'application/pdf'])
                    ->with('data', $this->data); // Kirim data ke view
    }
}
