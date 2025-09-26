<?php


namespace App\Http\Controllers;


use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;

class TestEmailController
{
    public function sendEmail()
    {
        $details = [
            'email' => 'saeful.arifin150@gmail.com',
            'subject' => 'Registrasi',
            'username' => 'Testing username',
            'password' => 'tetsingpassword'
        ];

        Mail::to($details['email'])->send(new TestEmail($details));

        return "Email telah dikirim!";
    }
}
