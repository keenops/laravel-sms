<?php

namespace Keenops\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Exception;

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

        try {
            $this->senderName = self::$config['beem_sender_name'];
            $this->apiKey = self::$config['beem_api_key'];
            $this->apiSecret = self::$config['beem_api_secret'];
        } catch (Exception $e) {
            throw new Exception("Failed to load SMS configuration: " . $e->getMessage());
        }
    }

    /**
     * Sends an SMS message to one or more recipients.
     *
     * @param string $message
     * @param array $recipients
     * @return \Illuminate\Http\Client\Response
     * @throws Exception
     */
    public function send(string $message, array $recipients)
    {
        try {
            $payload = [
                'source_addr' => $this->senderName,
                'schedule_time' => '',
                'encoding' => 0,
                'message' => $message,
                'recipients' => $this->formatRecipients($recipients),
            ];

            return $this->makeRequest('post', 'https://apisms.beem.africa/v1/send', $payload);
        } catch (Exception $e) {
            throw new Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves the current SMS credit balance.
     *
     * @return string
     * @throws Exception
     */
    public function viewBalance(): string
    {
        try {
            $response = $this->makeRequest('get', self::BASE_URL . '/vendors/balance');
            return json_decode($response)->data->credit_balance ?? 'N/A';
        } catch (Exception $e) {
            throw new Exception('Failed to fetch balance: ' . $e->getMessage());
        }
    }

    /**
     * Gets all sender names associated with your Beem account.
     *
     * @return \Illuminate\Http\Client\Response
     * @throws Exception
     */
    public function senderNames()
    {
        try {
            return $this->makeRequest('get', self::BASE_URL . '/sender-names');
        } catch (Exception $e) {
            throw new Exception('Failed to fetch sender names: ' . $e->getMessage());
        }
    }

    /**
     * Requests a new sender name for your Beem account.
     *
     * @param string $senderName
     * @param string $sampleContent
     * @return \Illuminate\Http\Client\Response
     * @throws Exception
     */
    public function requestNewSenderName(string $senderName, string $sampleContent)
    {
        try {
            $payload = [
                'senderid' => $senderName,
                'sample_content' => $sampleContent,
            ];

            return $this->makeRequest('post', self::BASE_URL . '/sender-names', $payload);
        } catch (Exception $e) {
            throw new Exception('Failed to request new sender name: ' . $e->getMessage());
        }
    }

    /**
     * Formats recipient numbers into Beem-required structure.
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
     * Makes an authenticated HTTP request to the Beem API.
     *
     * @param string $method
     * @param string $url
     * @param array|null $payload
     * @return \Illuminate\Http\Client\Response
     * @throws Exception
     */
    private function makeRequest(string $method, string $url, ?array $payload = null)
    {
        try {
            $client = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->apiKey, $this->apiSecret)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]);

            $response = $method === 'post'
                ? $client->post($url, $payload)
                : $client->get($url);

            $response->throw(); // Ensure HTTP errors are caught

            return $response;
        } catch (RequestException $e) {
            throw new Exception("Beem API request failed: " . $e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new Exception("Unexpected error during Beem API request: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}