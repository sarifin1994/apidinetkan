<?php


namespace App\Library;
use App\Models\Whatsapp\Mpwa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WaNdiing
{

    protected string $baseUrl;
    protected int $timeout;
    protected string $id;

    public function __construct()
    {
        $this->baseUrl = "http://103.184.122.170:3000/api";
        $this->timeout = 10;
    }

    protected function client()
    {
        return Http::timeout($this->timeout)
            ->acceptJson()
            ->contentType('application/json');
    }

    /* ================= TERMINAL ================= */

    public function create(array $data)
    {
        return $this->post('/terminal', $data);
    }

    public function all()
    {
        return $this->get('/terminal');
    }

    public function find()
    {
        return $this->get("/terminal/{$this->id}");
    }

    public function update(array $data)
    {
        return $this->patch("/terminal/{$this->id}", $data);
    }

    public function delete($id)
    {
        return $this->deleteRequest("/terminal/{$id}");
    }

    /* ================= AUTH ================= */

    public function sendCode(string $phone)
    {
        $reqq = $this->post("/terminal/{$this->id}/send-code", [
            'phone_number' => $phone
        ]);
        Log::info($reqq);
        return $reqq;
    }

    public function signIn(string $phone, string $code)
    {
        return $this->post("/terminal/{$this->id}/sign-in", [
            'phone_number' => $phone,
            'phone_code'   => $code
        ]);
    }

    public function logout()
    {
        return $this->post("/terminal/{$this->id}/log-out");
    }

    /* ================= CONTROL ================= */

    public function start($id)
    {
        return $this->post("/terminal/{$id}/start");
    }

    public function start_no_return($id)
    {
        return $this->post_no_return("/terminal/{$id}/start");
    }

    public function stop($id)
    {
        $this->post("/terminal/{$id}/stop");
//        return $this->post("/terminal/{$id}/stop");
    }

    public function startAll()
    {
        return $this->post('/terminal/start-all');
    }

    public function stopAll()
    {
        return $this->post('/terminal/stop-all');
    }

    /* ================= MESSAGE ================= */

    public function sendMessage($id, array $payload)
    {
        return $this->post("/terminal/{$id}/send", $payload);
    }

    /* ================= HTTP CORE ================= */

    protected function get(string $uri)
    {
        return $this->client()
            ->get($this->baseUrl.$uri)
            ->throw()
            ->json();
    }

    protected function post(string $uri, array $data = [])
    {
        return $this->client()
            ->post($this->baseUrl.$uri, $data)
//            ->throw()
            ->json();
    }

    protected function post_no_return(string $uri, array $data = [])
    {
        $this->client()
            ->post($this->baseUrl.$uri, $data);
//            ->throw()
//            ->json();
    }

    protected function patch(string $uri, array $data = [])
    {
        return $this->client()
            ->patch($this->baseUrl.$uri, $data)
            ->throw()
            ->json();
    }

    protected function deleteRequest(string $uri)
    {
        return $this->client()
            ->delete($this->baseUrl.$uri)
            ->throw()
            ->json();
    }

    public function getWhatsappTerminal()
    {
        $response = $this->all();

        return collect($response['data'] ?? [])
            ->firstWhere('channel', 'whatsapp');
    }

    public function getWhatsappId()
    {
        return $this->getWhatsappTerminal()['id'] ?? '';
    }

    public function authOtpRequest(string $phone)
    {
        return $this->post("/auth/otp/request", [
            "channel" => "whatsapp",
            "identifier" => "$phone"
        ]);
    }

    public function create_terminal($shortname){
        Log::info('create terminal => '. $shortname);
        $response = $this->create([
            'channel'          => 'whatsapp',
            'auto_start'       => 0,
            'config'           => (object)[],
            'webhook_inbound'  => (object)[],
            'webhook_outbound' => (object)[
                "method" => "POST",
                "url" => env('APP_URL')."notification/whatsapp/receive_qr/$shortname"
            ],
        ]);
        $mpwa = Mpwa::query()->where('shortname', $shortname)->first();
        $mpwa->update([
            'api_key' => $response['data']['id']
        ]);
    }

    public function uploadFile($file)
    {
        $response =  Http::withHeaders([
            'Content-Type' => $file->getMimeType(),
        ])
            ->withBody(
                fopen($file->getRealPath(), 'r'),
                $file->getMimeType()
            )
            ->post($this->baseUrl.'/file');
        if (!$response->successful()) {
            Log::error('UPLOAD API FILE FAILED', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        }
        if ($response->successful()) {
//            Log::error('UPLOAD API FILE success', [
//                'status' => $response->status(),
//                'body'   => $response->body(),
//            ]);
            return $response->json();
        }

        return $response;
    }
}
