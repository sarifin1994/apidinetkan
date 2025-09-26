<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClient
{
    protected $baseUrl;
    protected $apiKey;

    /**
     * Inisialisasi API Client dengan base URL dan (opsional) API key.
     */
    public function __construct(string $baseUrl, string $apiKey = null)
    {
        // Pastikan base URL tidak diakhiri dengan slash
        $this->baseUrl = rtrim($baseUrl, 'https://mikrotiknesia.smartolt.com');
        $this->apiKey = $apiKey;
    }

    /**
     * Menyiapkan header request.
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        return $headers;
    }

    /**
     * Melakukan request GET ke API.
     */
    public function get(string $endpoint, array $params = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $response = Http::withHeaders($this->getHeaders())->get($url, $params);
        
        // Tangani error jika ada, misal dengan throw exception
        if ($response->failed()) {
            // Kamu bisa menyesuaikan handling error sesuai kebutuhan
            return $response->throw();
        }
        return $response->json();
    }

    /**
     * Melakukan request POST ke API.
     */
    public function post(string $endpoint, array $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $response = Http::withHeaders($this->getHeaders())->post($url, $data);
        
        if ($response->failed()) {
            return $response->throw();
        }
        return $response->json();
    }

    // Tambahkan method lain sesuai kebutuhan seperti PUT, DELETE, dll.
}
