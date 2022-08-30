<?php

namespace Keenops\Sms;

use Illuminate\Support\Facades\Http;

class Sms
{
    private $sender_name;
    private $key;
    private $secret;

    
    public function __construct() {
        $this->sender_name = config('laravel-beem-sms.beem_sender_name');
        $this->key = config('laravel-beem-sms.beem_api_key');
        $this->secret = config('laravel-beem-sms.beem_api_secret');
    }

    /**
     * Send sms to single or multiple recipients.
     *
     * @param String $message
     * @param array $recipients
     * 
     * @return json
     */
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

    /**
     * See how many messages are left in your account
     * @return string
     */
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

    /**
     * Find a list of sender names associated with your beem account
     * @return string
     */
    public function senderNames()
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withBasicAuth(
            $this->key,
            $this->secret
        )->withHeaders([
            'Accept' => 'application/json',
            'content-type' => 'application/json' 
        ])->get('https://apisms.beem.africa/public/v1/sender-names');

        return $response;
    }

     /**
     * Send sms to single or multiple recipients.
     *
     * @param String $senderName; desired name for sender id, Name will show as from on sms
     * @param string $sampleContent; an example of message that will be sent using this sender name
     * 
     * @return json
     */
    public function requestNewSenderName(String $senderName, String $sampleContent)
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withBasicAuth(
            $this->key,
            $this->secret
        )->withHeaders([
            'Accept' => 'application/json',
            'content-type' => 'application/json' 
        ])->post('https://apisms.beem.africa/public/v1/sender-names', array(
            'senderid' => $senderName,
            'sample_content' => $sampleContent
        ));

        return $response;
    }
}