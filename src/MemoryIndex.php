<?php

namespace Reklama;

class MemoryIndex {
    private $data = [];

    public function addItem($date, $pos) {
        $this->data[$date] = $pos;
    }

    public function searchDate($date) {
        if(!isset($this->data[$date])) return null;

        return $this->data[$date];
    }
}
