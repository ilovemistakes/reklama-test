<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\BinaryStream;
use Reklama\Converter\DateToIntConverter;

class BinaryStreamTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testIO() {
        $filename = $this->getTempFilename();

        $buffer = new BinaryStream($filename);

        $date = '2015-01-02';
        $data = [1, 2, 3];

        $buffer->setColumnCount(3);
        $buffer->writeItem($date, $data);
        $buffer->setPosition(0);
        $res = $buffer->readItem();
        $buffer->close();

        $this->assertSame(pack('Ld*', DateToIntConverter::convert($date), $data[0], $data[1], $data[2]), file_get_contents($filename));

        unlink($filename);
    }
}
