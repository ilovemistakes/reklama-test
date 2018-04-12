<?php

namespace Reklama\Aggregator;

use Reklama\Stream\InputStream;
use Reklama\Stream\OutputStream;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Reklama\MemoryIndex;
use Reklama\Stream\BinaryStream;
use Reklama\Converter\DateToIntConverter;

/**
 * Группировка и суммирование за один проход по входному файлу.
 * Используется промежуточный двоичный буфер, в котором заведомо известен
 * размер одной строки данных в байтах, что позволяет мгновенно адресовать
 * данные. Также используется "индексный" файл, в котором для каждой даты
 * хранится смещение блока с результирующими данными в двоичном буфере.
 * Сделан для уменьшения 
 *
 * Для каждой строки входящих данных мы ищем в индексном файле адрес выходных
 * данных. Если нашли, то считываем их из буфера, суммируем с текущей строчкой
 * и записываем обратно. Если не нашли, то дописываем блок в конец буфера и добавляем
 * в индекс адрес блока. После обработки всех входных данных разворачиваем
 * двоичный буфер в человекочитаемый формат.
 */
class BinaryIndexedAggregator implements AggregatorInterface {
    public function run(OutputInterface $output, InputStream $in, OutputStream $out) {
        $output->writeln('Обработка данных...');

        $progress = new ProgressBar($output, $in->getSize());
        $progress->setRedrawFrequency(1024 * 1024); // 1 Mb
        $progress->start();

        $buffer_filename = tempnam(sys_get_temp_dir(), 'buffer');

        $index = new MemoryIndex();
        $buffer = new BinaryStream($buffer_filename);

        while(!$in->isEof()) {
            $pos = $in->getPosition();

            $row = $in->read();

            if(!is_array($row)) continue;

            $date = array_shift($row);

            // чтобы буфер знал сколько байт в блоке
            $buffer->setColumnCount(count($row));

            $buffer_pos = $index->searchDate($date);
            if($buffer_pos === null) {
                // даты нет в индексе
                $buffer->setPosition(0, SEEK_END);
                $buffer_pos = $buffer->getPosition();

                // дописываем данные в конец буфера
                $buffer->writeItem($date, $row);

                // добавляем адрес блока в индекс
                $index->addItem($date, $buffer_pos);
            } else {
                // дата есть в индексе
                $buffer->setPosition($buffer_pos);
                $prev_data = $buffer->readItem();

                array_shift($prev_data);

                // суммируем предыдущие данные за дату с текущими
                foreach($row as $i => &$value) {
                    $value += $prev_data[$i];
                }

                // записываем результат поверх старого блока
                $buffer->setPosition($buffer_pos);
                $buffer->writeItem($date, $row);
            }

            $progress->setProgress($pos);
        }
        $progress->finish();
        $output->writeln('');

        $output->writeln('Запись выходного файла...');
        $progress = new ProgressBar($output, $buffer->getPackCount());
        $progress->start();

        // конвертируемый двоичный буфер с результатом агрегации в текстовый формат
        $buffer->setPosition(0);
        while(!$buffer->isEof()) {
            $item = $buffer->readItem();
            if(empty($item)) continue;

            $item[0] = DateToIntConverter::unconvert($item[0]); // конвертируем дату в читаемый формат

            $out->write($item);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');

        $buffer->close();
        unlink($buffer_filename);
    }
}
