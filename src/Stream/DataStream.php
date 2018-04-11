<?php

namespace Reklama\Stream;

/**
 * Обёртка для потока данных
 */
abstract class DataStream {
    protected $f;

    protected $size;

    abstract protected function getFileMode();

    public function __construct($filename) {
        $this->size = filesize($filename);

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
    }

    public function getSize() {
        return $this->size;
    }
}
