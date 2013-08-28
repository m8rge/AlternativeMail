<?php namespace m8rge\tests;

use m8rge\AlternativeMail;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var AlternativeMail */
    public $mail;

    protected function setUp()
    {
        $this->mail = new AlternativeMail();
    }

    /**
     * @param string[] $expected
     * @param string $actual
     */
    public function assertHeaders($expected, $actual)
    {
        $actual = explode("\r\n", $actual);
        $this->assertSameSize($expected, $actual);
        foreach ($expected as $header) {
            $this->assertContains($header, $actual, "Failed asserting that an ".print_r($actual, true)."contains '$header'");
        }
    }
}
