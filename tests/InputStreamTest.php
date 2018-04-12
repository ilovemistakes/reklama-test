<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\InputStream;

class InputStreamTest extends TestCase {
    public function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testRead() {
        $filename = $this->getTempFilename();

        file_put_contents($filename,
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
        );

        $in = new InputStream($filename);

        $this->assertSame(['2015-01-02', '3', '2', '1'], $in->read());
        $this->assertSame(['2016-03-25', '100', '123.456', '-0.005'], $in->read());
        $this->assertFalse($in->read());
        $in->close();

        unlink($filename);

        $this->expectException(\LogicException::class);
        $in->read();
    }

    public function testGetFileMode() {
        $filename = $this->getTempFilename();

        $contents = 
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
            ;

        file_put_contents($filename, $contents);

        $in = new InputStream($filename);
        $in->read();
        $in->close();

        $this->assertSame($contents, file_get_contents($filename));
    }
}
