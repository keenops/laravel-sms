<?php

namespace Keenops\Sms;

use Illuminate\Support\Facades\Http;

class Sms
{
    /**
     * The configuration.
     *
     * @var array|null
     */
    private static ?array $config = null;

    /**
     * The sender name.
     *
     * @var string
     */
    private string $sender_name;

    /**
     * The API key.
     *
     * @var string
     */
    private string $key;

    /**
     * The API secret.
     *
     * @var string
     */
    private string $secret;

    /**
     * Create a new Sms instance.
     */
    public function __construct() {
        if (self::$config === null) {
            self::$config = config('laravel-beem-sms');
        }

        $this->sender_name = self::$config['beem_sender_name'];
        $this->key = self::$config['beem_api_key'];
        $this->secret = self::$config['beem_api_secret'];
    }

    /**
     * Prepare the recipients array.
     *
     * @param array $recipients
     * @return array
     */
    private function prepareRecipients(array $recipients): array
    {
        $receivers = array();
        foreach ($recipients as $index => $recipient) {
            $recipient = str_replace([' ', '-', '+'], '', $recipient);
            if (strlen($recipient) == 9) {
                $recipient = '255' . substr($recipient, 0);
            } elseif (strlen($recipient) == 10) {
                $recipient = '255' . substr($recipient, 1);
            }
            $receivers[] = array(
                'recipient_id' => $index,
                'dest_addr' => $recipient,
            );
        }

        return $receivers;
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
        $receivers = $this->prepareRecipients($recipients);

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

        $response = json_decode($response)->data->credit_balance;

        return $response;
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