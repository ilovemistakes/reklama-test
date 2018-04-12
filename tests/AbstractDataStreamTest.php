<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\AbstractDataStream;

class AbstractDataStreamTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getStreamMock() {
        $stub = $this->getMockForAbstractClass(AbstractDataStream::class, [], '', false, false, true, []);

        $stub->expects($this->any())
            ->method('getFileMode')
            ->will($this->returnValue('r'));

        return $stub;
    }

    public function testIsOpen() {
        $filename = $this->getTempFilename();

        $stub = $this->getStreamMock();

        file_put_contents($filename,
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
        );

        $stub->__construct($filename);
        $this->assertTrue($stub->isOpen());
        $stub->close();
        $this->assertFalse($stub->isOpen());

        unlink($filename);
    }

    public function testGetSize() {
        $filename = $this->getTempFilename();

        $stub = $this->getStreamMock();

        file_put_contents($filename,
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
        );

        $stub->__construct($filename);
        $this->assertSame(47, $stub->getSize());

        unlink($filename);
    }

    public function testGetPosition() {
        $filename = $this->getTempFilename();

        $stub = $this->getStreamMock();

        file_put_contents($filename,
            "2015-01-02;3;2;1" . PHP_EOL .
            "2016-03-25;100;123.456;-0.005" . PHP_EOL
        );

        $stub->__construct($filename);
        $this->assertFalse($stub->isEof());
        $this->assertSame(0, $stub->getPosition());
        $stub->setPosition(30);
        $this->assertSame(30, $stub->getPosition());
        $stub->setPosition(0, SEEK_END);
        $this->assertSame(47, $stub->getPosition());
        $this->assertFalse($stub->isEof());

        unlink($filename);
    }
}
