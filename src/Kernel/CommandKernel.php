<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Interface\CommandInterface;
use Fajar\Bandung\Interface\ContainerInterface;

class CommandKernel
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * this function handle request and response
     */
    public function run(): void
    {
        $this->container
            ->get(CommandInterface::class)
            ->dispatch();
    }
}