<?php

namespace Fajar\Bandung\Command;

use Fajar\Bandung\Attribute\Command;
use Fajar\Bandung\Interface\CommandInterface;

class ConsoleCommand implements CommandInterface
{
    public array $commands = [];

    public function addCommand(Command $command): self
    {
        $this->commands[$command->name] = $command;
        return $this;
    }

    public function dispatch()
    {

    }
}