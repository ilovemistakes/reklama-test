<?php

use PHPUnit\Framework\TestCase;

use Reklama\Stream\AbstractBinaryStream;

class AbstractBinaryStreamTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    protected function getStreamMock() {
        $stub = $this->getMockForAbstractClass(AbstractBinaryStream::class, [], '', false, false, true, []);

        $stub->expects($this->any())
            ->method('getFileMode')
            ->will($this->returnValue('w+'));

        return $stub;
    }

    public function testIO() {
        $filename = $this->getTempFilename();

        $stub = $this->getStreamMock();

        $stub->__construct($filename);
        $stub->write('hello');
        $stub->setPosition(0);
        $this->assertSame('hello', $stub->read(5));
        $stub->write(', world!');
        $stub->close();

        $this->assertSame('hello, world!', file_get_contents($filename));

        unlink($filename);
    }
}
