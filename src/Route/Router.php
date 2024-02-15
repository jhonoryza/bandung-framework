<?php

namespace Fajar\Bandung\Route;


use Fajar\Bandung\Attribute\Route;
use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Interface\RouterInterface;
use Fajar\Bandung\Response\Response;

class Router implements RouterInterface
{
    private array $routes = [];

    public function __construct()
    {
    }

    public function addRoute(Route $route): self
    {
        $this->routes[$route->method->value] = $route;
        return $this;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $route = $this->routes[$request->getMethod()->value] ?? null;
        if ($route instanceof Route && $route->uri === $request->getPath()) {
            // call controller method
            $controllerMethodName = $route->reflectionMethod->getName();
            $controllerClassName = $route->reflectionMethod->getDeclaringClass()->getName();
            $controller = new $controllerClassName();
            return $controller->{$controllerMethodName}($request);
        }
        return Response::make(HttpHeader::HTTP_404, 'not found');
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}