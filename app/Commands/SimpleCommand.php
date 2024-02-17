<?php

namespace App\Commands;

use Fajar\Bandung\Attribute\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

/**
 * playground command
 * this class is used for testing command
 */
class SimpleCommand
{
    #[Command('test:warning')]
    public function testWarning(): void
    {
        warning('testing warning ok');
    }

    #[Command('test:info')]
    public function testInfo(): void
    {
        info('testing info ok');
    }
}