<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Interface\ContainerInterface;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\RouterInterface;

class HttpKernel
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
        $request = $this->container->get(RequestInterface::class);
        $response = $this->container
            ->get(RouterInterface::class)
            ->dispatch($request);

        ob_start();

        foreach ($response->getHeaders() as $key => $value) {
            header("{$key}: {$value}");
        }
        echo $response->getBody();

        ob_end_flush();
    }
}