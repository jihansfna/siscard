<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Kirim pesan WhatsApp menggunakan Fonnte API
     *
     * @param string $nomor
     * @param string $pesan
     * @return bool
     */
    public function kirimPesan($nomor, $pesan)
    {
        if (!$this->token) {
            Log::warning('Fonnte Token is missing. WhatsApp message was not sent.');
            return false;
        }

        // Fonnte accepts numbers starting with 0 or 62. Ensure it's correctly formatted if needed.
        // We'll pass it exactly as it is for now, but usually it's best to format it.
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post('https://api.fonnte.com/send', [
                'target' => $nomor,
                'message' => $pesan,
                'delay' => '1',
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && $responseData['status'] == true) {
                    return true;
                }
                Log::error('Fonnte API returned an error: ' . $response->body());
                return false;
            }

            Log::error('Failed to send WhatsApp message via Fonnte: ' . $response->body());
            return false;
            
        } catch (\Exception $e) {
            Log::error('Exception caught when sending WhatsApp message via Fonnte: ' . $e->getMessage());
            return false;
        }
    }
}
