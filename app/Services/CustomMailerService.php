<?php
namespace App\Services;

use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Mailer;
use Symfony\Component\Mailer\Transport\TransportInterface;

class CustomMailerService
{
    protected MailManager $mailManager;

    public function __construct(MailManager $mailManager)
    {
        $this->mailManager = $mailManager;
    }

    public function sendWithUserSmtp(string $view, array $data, string $to, string $subject): void
    {
        $smtp = SmtpSetting::where('shortname', multi_auth()->shortname)->first();

        if (!$smtp) {
            throw new \Exception("User belum memiliki konfigurasi SMTP.");
        }
        // 1. Buat konfigurasi SMTP dinamis
        $transportConfig = [
            'transport' => 'smtp',
            'host' => $smtp->host,
            'port' => $smtp->port,
            'encryption' => $smtp->encryption,
            'username' => $smtp->username,
            'password' => $smtp->password,
        ];

        // 2. Buat transport SMTP dinamis
        $transport = $this->mailManager->createSymfonyTransport($transportConfig); // TransportInterface

        // 3. Buat mailer baru berbasis transport ini
        $mailer = new Mailer(
            'custom_user_mailer',
            app('view'),
            $transport,
            app('events')
        );

        // 4. Kirim email menggunakan mailer tersebut
        $mailer->send($view, $data, function ($message) use ($to, $subject, $smtp) {
            $message->to($to)
                ->from($smtp->username, 'Mailer User')
                ->subject($subject);
        });
    }



    public function sendWithUserSmtpCron(string $view, array $data, string $to, string $subject, SmtpSetting $smtp, ?string $pdfPath = null): void
    {
        if (!$smtp) {
            throw new \Exception("User belum memiliki konfigurasi SMTP.");
        }
        // 1. Buat konfigurasi SMTP dinamis
        $transportConfig = [
            'transport' => 'smtp',
            'host' => $smtp->host,
            'port' => $smtp->port,
            'encryption' => $smtp->encryption,
            'username' => $smtp->username,
            'password' => $smtp->password,
        ];

        // 2. Buat transport SMTP dinamis
        $transport = $this->mailManager->createSymfonyTransport($transportConfig); // TransportInterface

        // 3. Buat mailer baru berbasis transport ini
        $mailer = new Mailer(
            'custom_user_mailer',
            app('view'),
            $transport,
            app('events')
        );

        // 4. Kirim email menggunakan mailer tersebut
        $mailer->send($view, $data, function ($message) use ($to, $subject, $smtp, $pdfPath) {
            $message->to($to)
                ->from($smtp->username,$smtp->sender_name ?? 'Mailer User')
                ->subject($subject);

            if ($pdfPath && file_exists($pdfPath)) {
                $message->attach($pdfPath);
            }
        });
    }
}
