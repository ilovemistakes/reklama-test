<?php

namespace Reklama\Stream;

abstract class AbstractBinaryStream extends AbstractDataStream {
    public function write($data) {
        fwrite($this->f, $data);
    }

    public function read($length) {
        return fread($this->f, $length);
    }
}
