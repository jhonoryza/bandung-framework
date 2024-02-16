<?php

namespace Fajar\Bandung\Kernel;

use Fajar\Bandung\Attribute\Command;
use Fajar\Bandung\Attribute\Route;
use Fajar\Bandung\Command\ConsoleCommand;
use Fajar\Bandung\Container\Container;
use Fajar\Bandung\Enum\RouteMethod;
use Fajar\Bandung\Exception\ContainerNotFoundException;
use Fajar\Bandung\Interface\CommandInterface;
use Fajar\Bandung\Interface\ContainerInterface;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\RouterInterface;
use Fajar\Bandung\Request\Request;
use Fajar\Bandung\Route\Router;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * this class responsible to register all dependency
 * and register all route
 */
class Kernel
{
    private string $appNamespace = 'App\\';

    public function loadEnv($path): self
    {
        $lines = file($path . '/.env');
        foreach ($lines as $line) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            putenv(sprintf('%s=%s', $key, $value));
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
        return $this;
    }

    /**
     * this function will do the binding of all dependency
     * @throws ContainerNotFoundException
     */
    public function registerHttpDependency(string $appDir): Container
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

        $this->discoverRoutes($container, $appDir);

        return $container;
    }

    public function registerCommandDependency(string $appDir): Container
    {
        $container = new Container();

        Container::setInstance($container);

        $container
            ->singleton(Kernel::class, fn() => $this)
            ->singleton(ContainerInterface::class, fn() => $container)
            ->singleton(CommandInterface::class, fn() => new ConsoleCommand());

        // register user command
        $this->discoverCommand($container, $appDir);

        // override app namespace and register internal framework command
        $this->appNamespace = 'Fajar\\Bandung\\Command\\';
        $this->discoverCommand($container, __DIR__ . '/../Command');

        return $container;
    }

    /**
     * this function will register all routes from app folders
     * checking all php class that has Route attribute
     * then register it to Router class
     * @throws ContainerNotFoundException
     */
    private function discoverRoutes(Container $container, string $directory): void
    {
        $directories = new \RecursiveDirectoryIterator($directory);
        $files = new \RecursiveIteratorIterator($directories);
        foreach ($files as $file) {

            $fileName = $this->resolveFilename($file);
            if ($fileName === null) continue;

            $class = $this->resolveClass($directory, $file);
            if ($class === null) continue;

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (is_string($method)) {
                    try {
                        $method = new ReflectionClass($method);
                    } catch (ReflectionException) {
                        continue;
                    }
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

    /**
     * this function will register all command from app folders
     * checking all php class that has Command attribute
     * then register it to Console class
     * @throws ContainerNotFoundException
     */
    private function discoverCommand(Container $container, string $directory): void
    {
        $directories = new \RecursiveDirectoryIterator($directory);
        $files = new \RecursiveIteratorIterator($directories);
        foreach ($files as $file) {

            $fileName = $this->resolveFilename($file);
            if ($fileName === null) continue;

            $class = $this->resolveClass($directory, $file);
            if ($class === null) continue;

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (is_string($method)) {
                    try {
                        $method = new ReflectionClass($method);
                    } catch (ReflectionException) {
                        continue;
                    }
                }
                $commandAttribute = $method->getAttributes(Command::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
                $command = $commandAttribute?->newInstance();

                if ($command instanceof Command) {
                    $command->setReflectionMethod($method);
                    $container->get(CommandInterface::class)->addCommand($command);
                }
            }
        }
    }

    private function resolveFilename($file): string|null
    {
        $fileName = $file->getFilename();
        if (
            $fileName === ''
            || $fileName === '.'
            || $fileName === '..'
            || ucfirst($fileName) !== $fileName
        ) {
            return null;
        }
        return $fileName;
    }

    private function resolveClass(string $directory, $file): ReflectionClass|null
    {
        $className = str_replace(
            [$directory, '/', '.php', '\\\\'],
            [$this->appNamespace, '\\', '', '\\'],
            $file->getPathname(),
        );
        try {
            $class = new ReflectionClass($className);
        } catch (ReflectionException) {
            return null;
        }
        return $class;
    }
}