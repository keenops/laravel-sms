<?php

namespace Tests;

use Keenops\Sms\Sms;
use PHPUnit\Framework\TestCase;

class SmsTest extends TestCase
{
    public function testSendSmsSuccess() {
        $sms = new Sms();
        $response = $sms->send('Hello, World!', ['12345']);
        
        // Assert the response based on the test case
        $this->assertTrue($response['success']);
    }

    public function testViewBalanceSuccess() {
        $sms = new Sms();
        $response = $sms->viewBalance();

        // Assert the response based on the test case
        $this->assertIsInt($response);
    }

    public function testSenderNamesSuccess() {
        $sms = new Sms();
        $response = $sms->senderNames();

        // Assert the response based on the test case
        $this->assertIsArray($response);
    }

    // Additional test cases for other methods
}