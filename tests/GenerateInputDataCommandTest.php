<?php

use PHPUnit\Framework\TestCase;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Reklama\Command\GenerateInputDataCommand;

class GenerateInputDataCommandTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testExecute() {
        $filename = $this->getTempFilename();

        $app = new Application();
        $app->add(new GenerateInputDataCommand());

        $command = $app->find('generate-input-data');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'output' => $filename,
            '--line-count' => 5,
            '--column-count' => 2,
            '--day-count' => 10,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('Готово.', $output);

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($filename));

        $this->assertSame(5, count($data));
        $this->assertSame(3, count($data[0]));
        $this->assertSame(3, count($data[4]));

        unlink($filename);
    }
}
