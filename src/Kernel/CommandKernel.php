<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Attribute\Command;
use Fajar\Bandung\Interface\CommandInterface;
use Fajar\Bandung\Interface\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\warning;

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
     * this function handle request and response
     */
    public function run(): void
    {
        $commands = $this->container
            ->get(CommandInterface::class)
            ->getCommands();

        intro('bandung framework');

        if ($this->input->getFirstArgument() === null) {
            $listCommand = collect($commands)->keys()->join(PHP_EOL);
            warning('List available commands: ');
            info($listCommand);
            return;
        }

        info('running php bandung ' . $this->input->getFirstArgument());

        /** @var Command|null $command */
        $command = $commands[$this->input->getFirstArgument()] ?? null;
        if ($command === null) {
            error(message: 'command undefined');
            return;
        }

        $methodName = $command->getReflectionMethod()->getName();
        $class = $command->getReflectionMethod()->getDeclaringClass()->getName();
        $controller = new $class();
        $controller->{$methodName}();
    }
}