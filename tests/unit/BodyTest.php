<?php namespace m8rge\tests\unit;

use m8rge\tests\TestCase;

class BodyTest extends TestCase
{
    protected $textBody = 'I am text body';
    protected $textEncoded = "SSBhbSB0ZXh0IGJvZHk=";

    protected $htmlBody = '<hr />HTML IS AWESOME<hr />';
    protected $htmlEncoded = "PGhyIC8+SFRNTCBJUyBBV0VTT01FPGhyIC8+";

    public function testTextMail()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setTextBody($this->textBody)->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEquals($this->textEncoded, $message);
        $this->assertHeaders(
            array('Content-Type: text/plain; charset=UTF-8', 'Content-Transfer-Encoding: base64'),
            $additional_headers
        );
    }

    public function testHtmlMail()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail->setHtmlBody($this->htmlBody)->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);
        $this->assertEquals($this->htmlEncoded, $message);
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

    public function testMixedMail()
    {
        list($to, $subject, $message, $additional_headers) = $this->mail
            ->setTextBody($this->textBody)
            ->setHtmlBody($this->htmlBody)
            ->send();
        $this->assertEmpty($to);
        $this->assertEmpty($subject);

        $boundaryHeader = 'Content-Type: multipart/alternative; boundary=';
        $this->assertStringStartsWith($boundaryHeader, $additional_headers);
        $boundary = substr($additional_headers, strlen($boundaryHeader));

        $this->assertStringStartsWith("--$boundary\r\n", $message);
        $this->assertContains("--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64"
            . "\r\n\r\n{$this->textEncoded}\r\n\r\n", $message);
        $this->assertContains("--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64"
            . "\r\n\r\n{$this->htmlEncoded}\r\n\r\n", $message);
        $this->assertStringEndsWith("--$boundary--", $message);
    }

}
