<?php

namespace Reklama\Stream;

/**
 * Поток входных данных
 */
class InputStream extends AbstractDataStream {
    protected function getFileMode() {
        return 'r';
    }

    public function read() {
        if(!$this->isOpen()) {
            throw new \LogicException('Файл ещё не открыт');
        }

        return fgetcsv($this->f, 0, ';');
    }
}
