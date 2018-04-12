<?php

use PHPUnit\Framework\TestCase;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Reklama\Command\AggregateCommand;

class AggregateCommandTest extends TestCase {
    protected function getTempFilename() {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    public function testExecute() {
        $input = $this->getTempFilename();
        file_put_contents($input, <<<EOT
2018-03-01;3;4;5.05
2018-03-01;1;2;1
2018-03-01;2;2;-0.05
2018-03-02;5;7;6.06
2018-03-03;1;2;1.06
EOT
        );

        $expected_result = <<<EOT
2018-03-01;6;8;6
2018-03-02;5;7;6.06
2018-03-03;1;2;1.06
EOT
        ;


        $filename = $this->getTempFilename();

        $app = new Application();
        $app->add(new AggregateCommand());

        $command = $app->find('aggregate');

        foreach(['in-memory', 'seeker', 'binary', 'binary-indexed'] as $aggregator) {
            $commandTester = new CommandTester($command);
            $commandTester->execute([
                'command' => $command->getName(),
                'input' => $input,
                'output' => $filename,
                '--aggregator' => $aggregator,
            ]);

            $output = $commandTester->getDisplay();

            $this->assertContains('Готово.', $output);

            $this->assertSame($expected_result, trim(file_get_contents($filename)));

            unlink($filename);
        }

        unlink($input);
    }
}
