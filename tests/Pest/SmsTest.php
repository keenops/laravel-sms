<?php

use Illuminate\Support\Facades\Http;
use Keenops\Sms\Sms;

beforeEach(function () {
    config()->set('laravel-beem-sms', [
        'beem_sender_name' => 'TestSender',
        'beem_api_key' => 'test-key',
        'beem_api_secret' => 'test-secret',
    ]);

    $this->sms = new Sms();
});

it('sends sms successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(['status' => 'sent'], 200),
    ]);

    $response = $this->sms->send('Test message', ['0754123456']);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://apisms.beem.africa/v1/send' &&
               $request->method() === 'POST' &&
               $request['message'] === 'Test message';
    });

    expect($response->status())->toBe(200);
});

it('returns balance credit', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/vendors/balance' => Http::response([
            'data' => ['credit_balance' => '150.00']
        ], 200),
    ]);

    $balance = $this->sms->viewBalance();

    expect($balance)->toBe('150.00');
});

it('returns sender names', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/sender-names' => Http::response(['sender_names' => ['ABC']], 200),
    ]);

    $response = $this->sms->senderNames();

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKey('sender_names');
});

it('requests new sender name', function () {
    Http::fake([
        'https://apisms.beem.africa/public/v1/sender-names' => Http::response(['message' => 'Request submitted'], 200),
    ]);

    $response = $this->sms->requestNewSenderName('MySenderID', 'Hello sample');

    expect($response->status())->toBe(200)
        ->and($response['message'])->toBe('Request submitted');
});