<?php 

use Keenops\Sms\Sms;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'laravel-beem-sms.beem_sender_name' => 'MySender',
        'laravel-beem-sms.beem_api_key' => 'test-api-key',
        'laravel-beem-sms.beem_api_secret' => 'test-api-secret',
    ]);
});

it('can send an SMS', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(['success' => true], 200),
    ]);

    $sms = new Sms();
    $response = $sms->send('Hello from Pest!', ['0712345678']);

    $response->assertOk();
});

it('can get balance', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/vendors/balance' => Http::response([
            'data' => ['credit_balance' => '456.78']
        ], 200),
    ]);

    $sms = new Sms();
    $balance = $sms->viewBalance();

    expect($balance)->toBe('456.78');
});

it('can retrieve sender names', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/sender-names' => Http::response([
            'data' => ['names' => ['MySender']]
        ], 200),
    ]);

    $sms = new Sms();
    $response = $sms->senderNames();

    $response->assertOk();
});

it('can request a new sender name', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/sender-names' => Http::response([
            'message' => 'Request submitted'
        ], 200),
    ]);

    $sms = new Sms();
    $response = $sms->requestNewSenderName('NewSender', 'Sample content');

    $response->assertOk();
});