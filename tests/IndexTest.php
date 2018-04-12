<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\Index;

class IndexTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testIO() {
        $filename = $this->getTempFilename();

        $index = new Index($filename);

        $this->assertNull($index->searchDate('2015-01-02'));
        $index->addItem('2015-01-02', 123);
        $this->assertSame(123, $index->searchDate('2015-01-02'));
        $this->assertNull($index->searchDate('2015-01-03'));

        $index->close();

        unlink($filename);
    }
}
