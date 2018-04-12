<?php

namespace Reklama\Stream;

use Reklama\Converter\DateToIntConverter;

class Index extends AbstractBinaryStream {
    protected function getFileMode() {
        return 'w+';
    }

    protected function getPackLength() {
        return 4 + 8; // дата + позиция
    }

    public function addItem($date, $pos) {
        return $this->write(pack('LQ', DateToIntConverter::convert($date), $pos));
    }

    protected function readItem() {
        $data = $this->read($this->getPackLength());

        if(empty($data)) return null;

        return unpack('Ldate/Qpos', $data);
    }

    public function searchDate($date) {
        $this->setPosition(0);

        $date = DateToIntConverter::convert($date);

        while(!$this->isEof()) {
            $item = $this->readItem();

            if($item === null) break;

            if($date === $item['date']) {
                return $item['pos'];
            }
        }

        return null;
    }
}
