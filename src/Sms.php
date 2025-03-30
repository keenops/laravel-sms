<?php

namespace Keenops\Sms;

use Illuminate\Support\Facades\Http;

class Sms
{
    /**
     * Base URL for Beem API.
     */
    private const BASE_URL = 'https://apisms.beem.africa/public/v1';

    /**
     * Configuration array loaded from config file.
     */
    private static ?array $config = null;

    private string $senderName;
    private string $apiKey;
    private string $apiSecret;

    /**
     * Sms constructor initializes config only once.
     */
    public function __construct()
    {
        if (self::$config === null) {
            self::$config = config('laravel-beem-sms');
        }

        $this->senderName = self::$config['beem_sender_name'];
        $this->apiKey = self::$config['beem_api_key'];
        $this->apiSecret = self::$config['beem_api_secret'];
    }

    /**
     * Sends an SMS message to one or more recipients.
     *
     * @param string $message
     * @param array $recipients
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $message, array $recipients)
    {
        $payload = [
            'source_addr' => $this->senderName,
            'schedule_time' => '',
            'encoding' => 0,
            'message' => $message,
            'recipients' => $this->formatRecipients($recipients),
        ];

        return $this->makeRequest('post', 'https://apisms.beem.africa/v1/send', $payload);
    }

    /**
     * Retrieves the current SMS credit balance.
     *
     * @return string
     */
    public function viewBalance(): string
    {
        $response = $this->makeRequest('get', self::BASE_URL . '/vendors/balance');
        return json_decode($response)->data->credit_balance;
    }

    /**
     * Gets all sender names associated with your Beem account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function senderNames()
    {
        return $this->makeRequest('get', self::BASE_URL . '/sender-names');
    }

    /**
     * Requests a new sender name for your Beem account.
     *
     * @param string $senderName
     * @param string $sampleContent
     * @return \Illuminate\Http\Client\Response
     */
    public function requestNewSenderName(string $senderName, string $sampleContent)
    {
        $payload = [
            'senderid' => $senderName,
            'sample_content' => $sampleContent,
        ];

        return $this->makeRequest('post', self::BASE_URL . '/sender-names', $payload);
    }

    /**
     * Helper method to format recipients into API-required structure.
     *
     * @param array $recipients
     * @return array
     */
    private function formatRecipients(array $recipients): array
    {
        return array_map(function ($recipient, $index) {
            $clean = str_replace([' ', '-', '+'], '', $recipient);
            $length = strlen($clean);

            if ($length === 9) {
                $clean = '255' . $clean;
            } elseif ($length === 10) {
                $clean = '255' . substr($clean, 1);
            }

            return [
                'recipient_id' => $index,
                'dest_addr' => $clean,
            ];
        }, $recipients, array_keys($recipients));
    }

    /**
     * Generalized HTTP request with Beem authentication and headers.
     *
     * @param string $method  HTTP verb: get|post
     * @param string $url     Endpoint URL
     * @param array|null $payload  Optional request body
     * @return \Illuminate\Http\Client\Response
     */
    private function makeRequest(string $method, string $url, ?array $payload = null)
    {
        $client = Http::withOptions(['verify' => false])
            ->withBasicAuth($this->apiKey, $this->apiSecret)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        return $method === 'post' ? $client->post($url, $payload) : $client->get($url);
    }
}