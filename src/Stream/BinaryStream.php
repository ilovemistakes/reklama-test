<?php

namespace Reklama\Stream;

use Reklama\Converter\DateToIntConverter;

class BinaryStream extends AbstractBinaryStream {
    private $column_count;

    public function setColumnCount($count) {
        $this->column_count = $count;
    }

    protected function getFileMode() {
        return 'w+';
    }

    protected function getPackLength() {
        return 4 + $this->column_count * strlen(pack('d', 0.0)); // дата + позиция
    }

    public function writeItem($date, $data) {
        $this->write(
            call_user_func_array('pack', array_merge([
                'Ld*',
                DateToIntConverter::convert($date),
            ], $data))
        );
    }

    public function readItem() {
        $data = $this->read($this->getPackLength());

        if(empty($data)) return null;

        return array_values(unpack('Ldate/d*', $data));
    }

    public function getPackCount() {
        $old_pos = $this->getPosition();

        $this->setPosition(0, SEEK_END);
        $res = $this->getPosition();

        $this->setPosition($old_pos);

        return $res / $this->getPackLength();
    }
}
