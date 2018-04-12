<?php

use PHPUnit\Framework\TestCase;

use Reklama\Converter\DateToIntConverter;

class DateToIntConverterTest extends TestCase {
    public function testConvert() {
        $this->assertSame(20150102, DateToIntConverter::convert('2015-01-02'));
    }

    public function testUnconvert() {
        $this->assertSame('2017-12-23', DateToIntConverter::unconvert(20171223));
    }
}
