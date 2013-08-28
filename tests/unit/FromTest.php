<?php namespace m8rge\tests\unit;

use m8rge\tests\TestCase;

class FromTest extends TestCase
{
    public function testFrom()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setFrom('sender@mail.com')->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertHeaders(
            array('From: sender@mail.com'),
            $additional_headers
        );
    }

    public function testFromName()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setFrom('sender@mail.com', 'Sender Name')->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertHeaders(
            array('From: Sender Name <sender@mail.com>'),
            $additional_headers
        );
    }

    public function testUtf8FromName()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setFrom('sender@mail.com', 'Василий')->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertHeaders(
            array('From: =?UTF-8?B?0JLQsNGB0LjQu9C40Lk=?= <sender@mail.com>'),
            $additional_headers
        );
    }
}
