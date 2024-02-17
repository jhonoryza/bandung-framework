<?php

namespace Fajar\Bandung\Command;

use Fajar\Bandung\Attribute\Command;
use Fajar\Bandung\Interface\CommandInterface;

/**
 * this class is used for register commands
 */
class ConsoleCommand implements CommandInterface
{
    public array $commands = [];

    public function addCommand(Command $command): self
    {
        $this->commands[$command->name] = $command;
        return $this;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}