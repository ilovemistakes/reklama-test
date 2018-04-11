<?php

namespace Reklama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Reklama\Stream\OutputStream;

class GenerateInputDataCommand extends Command {
    protected function configure() {
        $this
            ->setName('generate-input-data')
            ->setDescription('Генерирует входной файл со случайными данными.')
            ->setHelp('Команда для удобства отладки алгоритма на разных объёмах данных.')
            ->addArgument('output', InputArgument::REQUIRED, 'Путь к файлу, в который требуется записать результат.')
            ->addOption('line-count', 'l', InputOption::VALUE_REQUIRED, 'Количество строк', 100)
            ->addOption('column-count', 'c', InputOption::VALUE_REQUIRED, 'Количество столбцов с измерениями (не включая дату)', 3)
            ->addOption('day-count', 'd', InputOption::VALUE_REQUIRED, 'Разброс генерируемых дат в днях', 10)
            ;
    }

    protected function generateRow($column_count, $day_count) {
        $res = [
            date('Y-m-d', strtotime('+' . mt_rand(0, $day_count) . ' days')),
        ];

        for($i = 0; $i < $column_count; $i++) {
            $res[] = mt_rand(-1000, 1000) / 10.0;
        }

        return $res;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $line_count = $input->getOption('line-count');

        $progress = new ProgressBar($output, $line_count);
        $progress->setRedrawFrequency(min(1000, $line_count / 1000));

        $result = new OutputStream($input->getArgument('output'));

        for($i = 0; $i < $line_count; $i++) {
            $result->write($this->generateRow($input->getOption('column-count'), $input->getOption('day-count')));

            $progress->setProgress($i);
        }

        $result->close();

        $progress->clear();

        $output->writeln('<info>Готово.</info>');
    }
}
