<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Attribute\Route;
use Fajar\Bandung\Container\Container;
use Fajar\Bandung\Enum\RouteMethod;
use Fajar\Bandung\Interface\ContainerInterface;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\RouterInterface;
use Fajar\Bandung\Request\Request;
use Fajar\Bandung\Route\Router;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * this class responsible to register all dependency
 * and register all route
 */
class Kernel
{
    /**
     * this function will do the binding of all dependency
     */
    public function registerProviders(): Container
    {
        $container = new Container();

        Container::setInstance($container);

        $container
            ->singleton(Kernel::class, fn() => $this)
            ->singleton(ContainerInterface::class, fn() => $container)
            ->singleton(RouterInterface::class, fn() => new Router())
            ->singleton(RequestInterface::class, function () {
                return new Request(
                    method: RouteMethod::tryFrom($_SERVER['REQUEST_METHOD']) ?? RouteMethod::GET,
                    uri: $_SERVER['REQUEST_URI'] ?? '/',
                    body: file_get_contents('php://input'),
                );
            });

        $this->discoverRoutes($container);

        return $container;
    }

    /**
     * this function will register all routes from app folders
     * checking all php class that has Route attribute
     * then register it to Router class
     */
    public function discoverRoutes($container): void
    {
        $directory = __DIR__ . '/../../app';
        $namespace = 'App\\';

        $directories = new \RecursiveDirectoryIterator($directory);
        $files = new \RecursiveIteratorIterator($directories);
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            if (
                $fileName === ''
                || $fileName === '.'
                || $fileName === '..'
                || ucfirst($fileName) !== $fileName
            ) {
                continue;
            }

            $className = str_replace(
                [$directory, '/', '.php', '\\\\'],
                [$namespace, '\\', '', '\\'],
                $file->getPathname(),
            );
            if (!class_exists($className)) {
                continue;
            }
            $class = new ReflectionClass($className);

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (is_string($method)) {
                    if (!class_exists($method)) {
                        continue;
                    }
                    $method = new ReflectionClass($method);
                }
                $routeAttribute = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
                $route = $routeAttribute?->newInstance();

                if ($route instanceof Route) {
                    $route->setReflectionMethod($method);
                    $container->get(RouterInterface::class)->addRoute($route);
                }
            }
        }

    }
}