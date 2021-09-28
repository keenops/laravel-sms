<?php

namespace Keenops\LaravelSms;

use Illuminate\Support\Facades\Http;

class LaravelSms
{
    public static function send(string $message, array $recipients)
    {

        $receiveres = array();
        foreach ($recipients as $index => $recipient) {
            $recipient = str_replace([' ', '-', '+'], '', $recipient);
            if (strlen($recipient) == 9) {
                $recipient = '255' . substr($recipient, 0);
            } elseif (strlen($recipient) == 10) {
                $recipient = '255' . substr($recipient, 1);
            }
            $receiveres[] = $list[] = array(
                'recipient_id' => $index,
                'dest_addr' => $recipient,
            );
        }

        $response = Http::withOptions([
            'verify' => false,
        ])->withBasicAuth(
            config('laravel-sms.beem_api_key'),
            config('laravel-sms.beem_api_secret')
        )->withHeaders([
            'Accept' => 'application/json',
            'content-type' => 'application/json' 
        ])->post('https://apisms.beem.africa/v1/send', array(
            'source_addr' => config('laravel-sms.beem_sender_name'),
            'schedule_time' => '',
            'encoding'  => 0,
            'message' => $message,
            'recipients' => $receiveres,
        ));

        return $response;
    }
}
