<?php

namespace Fajar\Bandung\Interface;

use Fajar\Bandung\Attribute\Command;

interface CommandInterface
{
    public function addCommand(Command $command): self;
}