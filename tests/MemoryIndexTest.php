<?php

use PHPUnit\Framework\TestCase;

use Reklama\MemoryIndex;

class MemoryIndexTest extends TestCase {
    public function testAddItem() {
        $index = new MemoryIndex();

        $this->assertNull($index->searchDate('2015-12-23'));
        $this->assertNull($index->searchDate('2016-02-10'));

        $index->addItem('2015-12-23', 234);

        $this->assertSame($index->searchDate('2015-12-23'), 234);
        $this->assertNull($index->searchDate('2016-02-10'));

        $index->addItem('2016-02-10', 567);

        $this->assertSame($index->searchDate('2015-12-23'), 234);
        $this->assertSame($index->searchDate('2016-02-10'), 567);

        $index->addItem('2016-02-10', 321);

        $this->assertSame($index->searchDate('2015-12-23'), 234);
        $this->assertSame($index->searchDate('2016-02-10'), 321);
    }

    public function testSearchDate() {
        $index = new MemoryIndex();

        $index->addItem('2015-12-23', 234);

        $this->assertSame($index->searchDate('2015-12-23'), 234);
        $this->assertNull($index->searchDate('2015-12-25'));
    }
}
