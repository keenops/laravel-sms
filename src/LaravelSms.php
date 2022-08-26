<?php

namespace Keenops\LaravelSms;

use Illuminate\Support\Facades\Http;

class LaravelSms
{
    private $sender_name;
    private $key;
    private $secret;

    public function __construct() {
        $this->sender_name = config('laravel-sms.beem_sender_name');
        $this->key = config('laravel-sms.beem_api_key');
        $this->secret = config('laravel-sms.beem_api_secret');
    }

    public function send(string $message, array $recipients)
    {
        $receivers = array();
        foreach ($recipients as $index => $recipient) {
            $recipient = str_replace([' ', '-', '+'], '', $recipient);
            if (strlen($recipient) == 9) {
                $recipient = '255' . substr($recipient, 0);
            } elseif (strlen($recipient) == 10) {
                $recipient = '255' . substr($recipient, 1);
            }
            $receivers[] = $list[] = array(
                'recipient_id' => $index,
                'dest_addr' => $recipient,
            );
        }

        $response = Http::withOptions([
            'verify' => false,
        ])->withBasicAuth(
            $this->key,
            $this->secret
        )->withHeaders([
            'Accept' => 'application/json',
            'content-type' => 'application/json' 
        ])->post('https://apisms.beem.africa/v1/send', array(
            'source_addr' => $this->sender_name,
            'schedule_time' => '',
            'encoding'  => 0,
            'message' => $message,
            'recipients' => $receivers,
        ));

        return $response;
    }

    public function viewBalance()
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withBasicAuth(
            $this->key,
            $this->secret
        )->withHeaders([
            'Accept' => 'application/json',
            'content-type' => 'application/json' 
        ])->get('https://apisms.beem.africa/public/v1/vendors/balance');

        return json_decode($response)->data->credit_balance;
    }
}
