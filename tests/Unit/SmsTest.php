<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use Keenops\Sms\Sms;
use Tests\TestCase;

class SmsTest extends TestCase
{
    protected Sms $sms;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind config manually if not using Laravel app config
        config()->set('laravel-beem-sms', [
            'beem_sender_name' => 'TestSender',
            'beem_api_key' => 'test-key',
            'beem_api_secret' => 'test-secret',
        ]);

        $this->sms = new Sms();
    }

    public function test_send_sms_successfully()
    {
        Http::fake([
            'https://apisms.beem.africa/v1/send' => Http::response(['status' => 'sent'], 200),
        ]);

        $response = $this->sms->send('Test message', ['0754123456']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://apisms.beem.africa/v1/send' &&
                   $request->method() === 'POST' &&
                   $request['message'] === 'Test message';
        });

        $this->assertEquals(200, $response->status());
    }

    public function test_view_balance_returns_credit()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/vendors/balance' => Http::response([
                'data' => ['credit_balance' => '150.00']
            ], 200),
        ]);

        $balance = $this->sms->viewBalance();

        $this->assertEquals('150.00', $balance);
    }

    public function test_sender_names_returns_response()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/sender-names' => Http::response(['sender_names' => ['ABC']], 200),
        ]);

        $response = $this->sms->senderNames();

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('sender_names', $response->json());
    }

    public function test_request_new_sender_name()
    {
        Http::fake([
            'https://apisms.beem.africa/public/v1/sender-names' => Http::response(['message' => 'Request submitted'], 200),
        ]);

        $response = $this->sms->requestNewSenderName('MySenderID', 'Hello sample');

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Request submitted', $response['message']);
    }
}