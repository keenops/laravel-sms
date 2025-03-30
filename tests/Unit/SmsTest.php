<?php

namespace Tests\Unit;

use Keenops\Sms\Sms;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'laravel-beem-sms.beem_sender_name' => 'MySender',
            'laravel-beem-sms.beem_api_key' => 'test-api-key',
            'laravel-beem-sms.beem_api_secret' => 'test-api-secret',
        ]);
    }

    public function test_it_can_send_sms()
    {
        Http::fake([
            'https://apisms.beem.africa/v1/send' => Http::response(['success' => true], 200),
        ]);

        $sms = new Sms();
        $response = $sms->send('Hello World', ['0712345678']);

        $response->assertOk();
    }

    public function test_it_can_get_balance()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/vendors/balance' => Http::response([
                'data' => ['credit_balance' => '123.45']
            ], 200),
        ]);

        $sms = new Sms();
        $balance = $sms->viewBalance();

        $this->assertEquals('123.45', $balance);
    }

    public function test_it_can_get_sender_names()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/sender-names' => Http::response([
                'data' => ['names' => ['MySender']]
            ], 200),
        ]);

        $sms = new Sms();
        $response = $sms->senderNames();

        $response->assertOk();
    }

    public function test_it_can_request_new_sender_name()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/sender-names' => Http::response(['message' => 'Request submitted'], 200),
        ]);

        $sms = new Sms();
        $response = $sms->requestNewSenderName('MyNewSender', 'Sample content');

        $response->assertOk();
    }
}