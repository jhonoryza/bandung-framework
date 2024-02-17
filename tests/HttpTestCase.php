<?php

namespace Tests;

use Fajar\Bandung\Container\Container;
use Fajar\Bandung\Enum\RouteMethod;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Interface\RouterInterface;
use Fajar\Bandung\Kernel\Kernel;
use Fajar\Bandung\Request\Request;
use Fajar\Bandung\Route\Router;
use Illuminate\Testing\AssertableJsonString;
use PHPUnit\Framework\TestCase;

class HttpTestCase extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = (new Kernel)
            ->loadEnv(__DIR__ . '/../.env')
            ->registerHttpDependency(__DIR__ . '/../app');
    }

    protected function getRequest(string $uri): ResponseInterface
    {
        /**@var Router $router */
        $router = $this->container->get(RouterInterface::class);
        $request = new Request(method: RouteMethod::GET, uri: $uri, body: '');
        return $router->dispatch($request);
    }

    protected function json(string $json): AssertableJsonString
    {
        return (new AssertableJsonString($json));
    }
}