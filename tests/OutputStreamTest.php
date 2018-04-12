<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\OutputStream;

class OutputStreamTest extends TestCase {
    public function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testWrite() {
        $filename = $this->getTempFilename();

        $out = new OutputStream($filename);

        $out->write(['2015-01-02', 3, 2, 1]);
        $out->write(['2016-03-25', 100.0, 123.456, -0.005]);
        $out->close();

        $this->assertSame(
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
            , file_get_contents($filename)
        );

        unlink($filename);

        $this->expectException(\LogicException::class);
        $out->write(['2017-02-03', 1, 2, 3]);
    }
}
