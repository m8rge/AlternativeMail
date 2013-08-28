<?php namespace m8rge\tests\unit;

use m8rge\tests\TestCase;

class ToTest extends TestCase
{
    public function testTo()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->addTo('recipient@mail.com')->send();
        $this->assertEquals('recipient@mail.com', $to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }

    public function testToName()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->addTo('recipient@mail.com', 'First Second name')->send();
        $this->assertEquals('First Second name <recipient@mail.com>', $to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }

    public function testUtf8ToName()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->addTo('recipient@mail.com', 'Василий Алибабаевич')->send();
        $this->assertEquals('=?UTF-8?B?0JLQsNGB0LjQu9C40Lkg0JDQu9C40LHQsNCx0LDQtdCy0LjRhw==?= <recipient@mail.com>', $to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }

    public function testSeveralRecipients()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail
            ->addTo('recipient@mail.com', 'Василий Алибабаевич')
            ->addTo('recipient2@mail.com')
            ->addTo('recipient3@mail.com', 'John')
            ->send();
        $this->assertEquals('=?UTF-8?B?0JLQsNGB0LjQu9C40Lkg0JDQu9C40LHQsNCx0LDQtdCy0LjRhw==?= <recipient@mail.com>, recipient2@mail.com, John <recipient3@mail.com>', $to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }
}
