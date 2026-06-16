<?php

namespace App\Services;

class WhatsappService
{
    /**
     * Fungsi Global untuk Mengirim Pesan WhatsApp via Fonnte API
     */
    public static function sendMessage($target, $message)
    {
        // Mengambil token otomatis dari file .env
        $token = env('FONNTE_TOKEN');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Memastikan nomor lokal Indonesia otomatis terformat dengan benar
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        return $response;
    }
}