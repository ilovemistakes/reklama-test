<?php

namespace Reklama\Aggregator;

use Reklama\Stream\InputStream;
use Reklama\Stream\OutputStream;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Агрегатор данных. Делает всю работу по обработке данных, выводит
 * прогресс пользователю по ходу работы.
 */
interface AggregatorInterface {
    public function run(OutputInterface $output, InputStream $in, OutputStream $out);
}
