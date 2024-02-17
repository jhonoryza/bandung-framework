<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Attribute\Command;
use Fajar\Bandung\Command\ConsoleCommand;
use Fajar\Bandung\Interface\CommandInterface;
use Fajar\Bandung\Interface\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\warning;

/**
 * this class responsible to handle console command input argument
 */
class CommandKernel
{
    private ContainerInterface $container;
    private ArgvInput $input;

    public function __construct(ContainerInterface $container, ArgvInput $input)
    {
        $this->container = $container;
        $this->input = $input;
    }

    /**
     * this function will be called from bandung file
     */
    public function run(): void
    {
        /** @var ConsoleCommand $command */
        $consoleCommand = $this->container->get(CommandInterface::class);
        $commands = $consoleCommand->getCommands();

        intro('bandung framework');

        // render all available command if no argument given
        if ($this->input->getFirstArgument() === null) {
            $listCommand = collect($commands)->keys()->join(PHP_EOL);
            warning('List available commands: ');
            info($listCommand);
            return;
        }

        info('running php bandung ' . $this->input->getFirstArgument());

        // check if command exist or registered
        /** @var Command|null $command */
        $command = $commands[$this->input->getFirstArgument()] ?? null;
        if ($command === null) {
            error(message: 'command undefined');
            return;
        }

        // run command
        $methodName = $command->getReflectionMethod()->getName();
        $class = $command->getReflectionMethod()->getDeclaringClass()->getName();
        $controller = new $class();
        $controller->{$methodName}();
    }
}