<?php

namespace Fajar\Bandung\Route;


use Fajar\Bandung\Attribute\Route;
use Fajar\Bandung\Exception\ContainerNotFoundException;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Interface\RouterInterface;

/**
 * this class will handle route
 * matching requested route to registered routes
 * then calling appropriate method from controller
 */
class Router implements RouterInterface
{
    private array $routes = [];

    public function __construct()
    {
    }

    /**
     * register route
     */
    public function addRoute(Route $route): self
    {
        $this->routes[$route->method->value][] = $route;
        return $this;
    }

    /**
     * @throws ContainerNotFoundException
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        // if no routes registered, return 404
        $routes = $this->routes[$request->getMethod()->value] ?? null;
        if ($routes === null) {
            return response()->notFound();
        }

        $matchedRoute = null;
        $params = null;
        /** @var Route $route */
        foreach ($routes as $route) {
            $params = $this->resolveParams($route->uri, $request->getPath());

            // if params found, break loop
            if ($params !== null) {
                $matchedRoute = $route;
                break;
            }
        }

        // if no matched route, return 404
        if ($params === null) {
            return response()->notFound();
        }

        // find controller and method from matched route
        $controllerMethodName = $matchedRoute->reflectionMethod->getName();
        $controllerClassName = $matchedRoute->reflectionMethod->getDeclaringClass()->getName();
        $controller = app()->get($controllerClassName);

        /** resolve missing params that not defined in route
         * like something that is injected manually in controller parameter
         */
        foreach ($matchedRoute->reflectionMethod->getParameters() as $param) {
            $paramName = $param->getName();
            if (!array_key_exists($paramName, $params)) {
                $paramType = $param->getType()->getName();
                $params[$paramName] = app()->get($paramType);
            }
        }

        // call controller method with params
        return call_user_func_array([$controller, $controllerMethodName], $params);
        // return $controller->{$controllerMethodName}(...$params);
    }

    /**
     * this function will resolve params from route uri
     */
    private function resolveParams(string $routeUri, string $requestPath): ?array
    {
        if ($routeUri === $requestPath) {
            return [];
        }

        $result = preg_match_all('/\{\w+}/', $routeUri, $tokens);

        if (!$result) {
            return null;
        }

        $tokens = $tokens[0];

        $matchingRegex = '/^' . str_replace(
                ['/', ...$tokens],
                ['\\/', ...array_fill(0, count($tokens), '([\w\d\s]+)')],
                $routeUri,
            ) . '$/';

        $result = preg_match_all($matchingRegex, $requestPath, $matches);

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