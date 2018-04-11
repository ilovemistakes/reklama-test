<?php

namespace Reklama\Aggregator;

use Reklama\Stream\InputStream;
use Reklama\Stream\OutputStream;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Решение "в лоб" - вычитываем всё в память, считаем и пишем.
 * Используется для сверки результатов на небольших данных.
 */
class InMemoryAggregator implements AggregatorInterface {
    public function run(OutputInterface $output, InputStream $in, OutputStream $out) {
        $output->writeln('Чтение входного файла...');

        // читаем ВЕСЬ входной файл
        $data = [];
        while(!$in->isEof()) {
            $row = $in->read();

            if(is_array($row)) {
                $data[] = $row;
            }
        }

        $output->writeln('Группировка...');
        $progress = new ProgressBar($output, count($data));
        $progress->setRedrawFrequency(count($data) / 1000);

        $progress->start();

        // группируем измерения по дате
        $res = [];
        foreach($data as $row) {
            $date = array_shift($row);

            if(!isset($res[$date])) $res[$date] = [];

            $res[$date][] = $row;

            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');
    
        $output->writeln('Суммирование и запись выходного файла...');
        $progress = new ProgressBar($output, count($res));
        $progress->start();

        // суммируем значения и пишем их в выходной файл
        foreach($res as $date => $rows) {
            $row = array_reduce($rows, function($sum, $row) {
                foreach($sum as $i => &$value) {
                    $value += $row[$i];
                }

                return $sum;
            }, array_fill(0, count($rows[0]), 0.0));
            array_unshift($row, $date);

            $out->write($row);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');
    }
}
