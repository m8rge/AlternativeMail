<?php namespace m8rge\tests\unit;

use m8rge\tests\TestCase;

class BodyTest extends TestCase
{
    public function testTextMail()
    {
        $textBody = 'I am text body';
        list($to, $subject, $message, $additional_headers) = $this->mail->setTextBody($textBody)->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEquals(base64_encode($textBody), $message);
        $this->assertHeaders(
            array('Content-Type: text/plain; charset=UTF-8', 'Content-Transfer-Encoding: base64'),
            $additional_headers
        );
    }

    public function testHtmlMail()
    {
        $htmlBody = '<hr />HTML IS AWESOME<hr />';
        list($to, $subject, $message, $additional_headers) = $this->mail->setHtmlBody($htmlBody)->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEquals(base64_encode($htmlBody), $message);
        $this->assertHeaders(
            array('Content-Type: text/html; charset=UTF-8', 'Content-Transfer-Encoding: base64'),
            $additional_headers
        );
    }

    public function testEmptyMail()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEmpty($message);
        $this->assertEmpty($additional_headers);
    }

}
