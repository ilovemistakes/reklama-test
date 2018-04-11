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

class AggregateCommand extends Command {
    protected function configure() {
        $this
            ->setName('aggregate')
            ->setDescription('Агрегирует входные данные по дате и суммирует измерения.')
            ->setHelp('Команда, выполняющая задачу из тестового задания.')
            ->addArgument('input', InputArgument::REQUIRED, 'Путь к файлу со входными данными.')
            ->addArgument('output', InputArgument::REQUIRED, 'Путь к файлу, в который требуется записать результат.')
            ->addOption('aggregator', 'a', InputOption::VALUE_REQUIRED, 'Алгоритм агрегации. Доступные алгоритмы: in-memory.', 'in-memory')
            ;
    }

    protected function createAggregator($name) {
        switch($name) {
        case 'in-memory':
            return new InMemoryAggregator();
        default:
            throw new \InvalidArgumentException(sprintf('Неизвестный агрегатор: ""', $name));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $in = new InputStream($input->getArgument('input'));
        $out = new OutputStream($input->getArgument('output'));

        $aggregator = $this->createAggregator($input->getOption('aggregator'), $in, $out);

        $aggregator->run($output, $in, $out);

        $output->writeln('<info>Готово.</info>');

        $output->writeln(sprintf('Пиковое выделение памяти: <info>%s</info> Мб.', round(memory_get_peak_usage(true) / 1024 / 1024)));
    }
}
