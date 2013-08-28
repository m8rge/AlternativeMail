<?php namespace m8rge\tests\unit;

use m8rge\tests\TestCase;

class SubjectTest extends TestCase
{
    public function testSubject()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setSubject('Example Subject')->send();
        $this->assertEmpty($to);
        $this->assertEquals('Example Subject', $subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }

    public function testUtf8Subject()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setSubject('Сложный Заголовок')->send();
        $this->assertEmpty($to);
        $this->assertEquals('=?UTF-8?B?0KHQu9C+0LbQvdGL0Lkg0JfQsNCz0L7Qu9C+0LLQvtC6?=', $subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }
}