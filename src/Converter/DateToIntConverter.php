<?php

namespace Reklama\Converter;

class DateToIntConverter {
    static public function convert($date) {
        return intval(str_replace('-', '', $date));
    }

    static public function unconvert($date) {
        $date = strval($date);

        return sprintf(
            '%s-%s-%s',
            substr($date, 0, 4),
            substr($date, 4, 2),
            substr($date, 6, 2)
        );
    }
}
