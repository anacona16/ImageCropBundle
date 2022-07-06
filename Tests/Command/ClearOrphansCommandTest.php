<?php

namespace Anacona16\Bundle\ImageCropBundle\Tests\Command;

use Anacona16\Bundle\ImageCropBundle\Command\ClearOrphansCommand;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ClearOrphansCommandTest extends WebTestCase
{
    protected CommandTester | null $commandTester;

    /**
     * This helper method abstracts the boilerplate code needed to test the
     * execution of a command.
     * @link https://symfony.com/doc/current/console.html#testing-commands
     */
    protected function executeCommand(string $commandClass, array $arguments = [], array $inputs = [], int $expectedExitCode=0): CommandTester
    {
        // this uses a special testing container that allows you to fetch private services

        if(!is_subclass_of($commandClass, Command::class))
        {throw new InvalidArgumentException("Not a command class");}
        #$this->assertInstanceOf(Command::class, $commandClass);

        $cmd = static::getContainer()->get($commandClass);
        $cmd->setApplication(new Application('Test'));

        $commandTester = new CommandTester($cmd);
        $commandTester->setInputs($inputs);
        $result = $commandTester->execute($arguments, ["capture_stderr_separately"]);

        $this->assertSame($expectedExitCode, $result);

        if($result !== $expectedExitCode)
        {var_dump($commandTester->getErrorOutput());}

        return $commandTester;
    }


    public function testClearOrphansCommand()
    {
        # error (path does not exist)
        $output = $this->executeCommand(ClearOrphansCommand::class, [], [], 0)->getDisplay();
        #$this->assertStringContainsString('successfully', $output);
    }

}
