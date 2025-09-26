<?php

namespace App\Library;

class ZteAPI
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $connection;

    public function __construct()
    {
        $this->host = env('OLT_HOST', '103.191.165.101');
        $this->port = env('OLT_PORT', 361);
        $this->username = env('OLT_USER', 'zte');
        $this->password = env('OLT_PASS', 'zte');
    }

    public function connect()
    {
        $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, 10);

        if (!$this->connection) {
            return ['status' => 'error', 'message' => "Koneksi gagal: $errstr ($errno)"];
        }

        stream_set_timeout($this->connection, 2);
        sleep(1);
        fread($this->connection, 1024);

        // Kirim username
        fwrite($this->connection, $this->username . "\n");
        sleep(2);

        // Kirim password
        fwrite($this->connection, $this->password . "\n");
        sleep(2);

        return ['status' => 'success', 'message' => 'Koneksi berhasil'];
    }

    public function executeCommand($command)
    {
        if (!$this->connection) {
            return ['status' => 'error', 'message' => 'Belum terkoneksi ke OLT'];
        }

        // Kirim perintah
        fwrite($this->connection, $command . "\n");
        sleep(2);

        // Ambil output
        $output = fread($this->connection, 4096);
        fclose($this->connection);

        return [
            'status' => 'success',
            'command' => $command,
            'output' => nl2br(htmlspecialchars($output))
        ];
    }
}
