<?php

namespace Reklama\Stream;

/**
 * Поток выходных данных
 */
class OutputStream extends DataStream {
    protected function getFileMode() {
        return 'w';
    }

    public function write($data) {
        if(!$this->isOpen()) {
            throw new \LogicException('Файл ещё не открыт');
        }

        fputcsv($this->f, $data, ';');
    }
}
