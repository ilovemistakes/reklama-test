<?php

namespace Reklama\Stream;

/**
 * Поток входных данных
 */
class InputStream extends DataStream {
    protected function getFileMode() {
        return 'r';
    }

    public function read() {
        if(!$this->isOpen()) {
            throw new \LogicException('Файл ещё не открыт');
        }

        return fgetcsv($this->f, 0, ';');
    }

    public function isEof() {
        return feof($this->f);
    }

    public function getPosition() {
        return ftell($this->f);
    }

    public function setPosition($position) {
        return fseek($this->f, $position);
    }
}
