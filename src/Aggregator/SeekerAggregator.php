<?php

namespace Reklama\Aggregator;

use Reklama\Stream\InputStream;
use Reklama\Stream\OutputStream;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * За первый проход по файлу для каждой даты запоминает все позиции
 * соответствующих строк. Затем для каждой даты рыщет по файлу,
 * вычитывает строки, суммирует данные и сразу же пишет в выходной файл.
 */
class SeekerAggregator implements AggregatorInterface {
    public function run(OutputInterface $output, InputStream $in, OutputStream $out) {
        $output->writeln('Чтение входного файла...');

        $progress = new ProgressBar($output, $in->getSize());
        $progress->setRedrawFrequency(1024 * 1024); // 1 Mb
        $progress->start();

        $data = [];
        while(!$in->isEof()) {
            $pos = $in->getPosition();

            $row = $in->read();

            if(is_array($row)) {
                $date = $row[0];

                if(!isset($data[$date])) $data[$date] = [];

                $data[$date][] = $pos;
            }

            $progress->setProgress($pos);
        }
        $progress->finish();
        $output->writeln('');

        $output->writeln('Суммирование и запись выходного файла...');
        $progress = new ProgressBar($output, count($data));
        $progress->start();

        foreach($data as $date => $positions) {
            $sum = null;
            foreach($positions as $position) {
                $in->setPosition($position);
                $row = $in->read();

                array_shift($row); // убираем дату

                if($sum === null) $sum = array_fill(0, count($row), 0.0);

                foreach($sum as $i => &$value) {
                    $value += $row[$i];
                }
            }

            array_unshift($sum, $date);

            $out->write($sum);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('');
    }
}
