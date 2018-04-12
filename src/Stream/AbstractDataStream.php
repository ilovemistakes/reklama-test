<?php

namespace Reklama\Stream;

/**
 * Обёртка для потока данных
 */
abstract class AbstractDataStream {
    protected $f;

    protected $size;

    abstract protected function getFileMode();

    public function __construct($filename) {
        $this->size = file_exists($filename) ? filesize($filename) : 0;

        $f = fopen($filename, $this->getFileMode());

        if($f === false) {
            throw new \InvalidArgumentException(sprintf('Ошибка открытия файла "%s"', $filename));
        }

        $this->f = $f;
    }

    public function isOpen() {
        return $this->f !== null;
    }

    public function close() {
        if(!$this->isOpen()) {
            throw new \LogicException('Файл ещё не открыт');
        }

        fclose($this->f);
        $this->f = null;
    }

    public function getSize() {
        return $this->size;
    }

    public function isEof() {
        return feof($this->f);
    }

    public function getPosition() {
        return ftell($this->f);
    }

    public function setPosition($position, $whence = SEEK_SET) {
        return fseek($this->f, $position, $whence);
    }
}
