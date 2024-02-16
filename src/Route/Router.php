<?php

namespace Fajar\Bandung\Route;


use Fajar\Bandung\Attribute\Route;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Interface\RouterInterface;

class Router implements RouterInterface
{
    private array $routes = [];

    public function __construct()
    {
    }

    public function addRoute(Route $route): self
    {
        $this->routes[$route->method->value][] = $route;
        return $this;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $routes = $this->routes[$request->getMethod()->value] ?? null;
        if ($routes === null) {
            return response()->notFound();
        }
        $matchedRoute = null;
        $params = null;
        /** @var Route $route */
        foreach ($routes as $route) {
            $params = $this->resolveParams($route->uri, $request->getPath());
            if ($params !== null) {
                $matchedRoute = $route;
                break;
            }
        }

        if ($params === null) {
            return response()->notFound();
        }

        // matched route
        $controllerMethodName = $matchedRoute->reflectionMethod->getName();
        $controllerClassName = $matchedRoute->reflectionMethod->getDeclaringClass()->getName();
        $controller = app()->get($controllerClassName);

        // resolve missing params like something injected by dependency injection
        foreach ($matchedRoute->reflectionMethod->getParameters() as $param) {
            $paramName = $param->getName();
            if (!array_key_exists($paramName, $params)) {
                $paramType = $param->getType()->getName();
                $params[$paramName] = app()->get($paramType);
            }
        }
        // call controller method
        return call_user_func_array([$controller, $controllerMethodName], $params);
        // return $controller->{$controllerMethodName}(...$params);
    }

    private function resolveParams(string $uri, string $path): ?array
    {
        if ($uri === $path) {
            return [];
        }

        $result = preg_match_all('/\{\w+}/', $uri, $tokens);

        if (!$result) {
            return null;
        }

        $tokens = $tokens[0];

        $matchingRegex = '/^' . str_replace(
                ['/', ...$tokens],
                ['\\/', ...array_fill(0, count($tokens), '([\w\d\s]+)')],
                $uri,
            ) . '$/';

        $result = preg_match_all($matchingRegex, $path, $matches);

        if ($result === 0) {
            return null;
        }

        unset($matches[0]);

        $matches = array_values($matches);

        $valueMap = [];

        foreach ($matches as $i => $match) {
            $valueMap[trim($tokens[$i], '{}')] = $match[0];
        }

        return $valueMap;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}