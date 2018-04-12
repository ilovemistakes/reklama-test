<?php

namespace Reklama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Reklama\Stream\InputStream;
use Reklama\Stream\OutputStream;
use Reklama\Aggregator\InMemoryAggregator;
use Reklama\Aggregator\SeekerAggregator;
use Reklama\Aggregator\BinaryAggregator;
use Reklama\Aggregator\BinaryIndexedAggregator;

class AggregateCommand extends Command {
    protected function configure() {
        $this
            ->setName('aggregate')
            ->setDescription('Агрегирует входные данные по дате и суммирует измерения.')
            ->setHelp('Команда, выполняющая задачу из тестового задания.')
            ->addArgument('input', InputArgument::REQUIRED, 'Путь к файлу со входными данными.')
            ->addArgument('output', InputArgument::REQUIRED, 'Путь к файлу, в который требуется записать результат.')
            ->addOption('aggregator', 'a', InputOption::VALUE_REQUIRED, 'Алгоритм агрегации. Доступные алгоритмы: binary, binary-indexed, seeker, in-memory.', 'seeker')
            ;
    }

    protected function createAggregator($name) {
        switch($name) {
        case 'in-memory':
            return new InMemoryAggregator();
        case 'seeker':
            return new SeekerAggregator();
        case 'binary':
            return new BinaryAggregator();
        case 'binary-indexed':
            return new BinaryIndexedAggregator();
        default:
            throw new \InvalidArgumentException(sprintf('Неизвестный агрегатор: "%s"', $name));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $in = new InputStream($input->getArgument('input'));
        $out = new OutputStream($input->getArgument('output'));

        $started_at = microtime(true);

        $aggregator = $this->createAggregator($input->getOption('aggregator'), $in, $out);

        $aggregator->run($output, $in, $out);

        $output->writeln('<info>Готово.</info>');

        $run_time = microtime(true) - $started_at;

        $output->writeln(sprintf('Пиковое выделение памяти: <info>%s</info> Мб.', round(memory_get_peak_usage(true) / 1024 / 1024)));
        $output->writeln(sprintf('Время выполнения скрипта: <info>%s</info>м <info>%s</info>с.', floor($run_time / 60), $run_time % 60));
    }
}
