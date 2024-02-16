<?php

namespace App\Commands;

use Fajar\Bandung\Attribute\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class TestCommand
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