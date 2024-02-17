<?php

namespace Fajar\Bandung\Container;

use Exception;
use Fajar\Bandung\Exception\ContainerNotFoundException;
use Fajar\Bandung\Interface\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    public array $binds = [];
    public array $singletons = [];
    private static self $instance;

    /*
     * this function is used to register bind
     */
    public function register(string $className, callable $callback): self
    {
        $this->binds[$className] = $callback;

        return $this;
    }

    /**
     * this function is used to resolved singleton or bind from the container
     * @throws ContainerNotFoundException
     */
    public function get(string $className): object
    {
        if ($instance = $this->singletons[$className] ?? null) {
            return $instance;
        }

        try {
            return $this->resolve($className); // the tree dots will convert to closure
        } catch (Throwable $throwable) {
            throw new ContainerNotFoundException('Target class [' . $className . '] does not exist.', 0, $throwable);
        }
    }

    /**
     * this function is used to resolved class from the container
     * @throws Exception|ReflectionException
     */
    private function resolve(string $className): object
    {
        try {
            $reflector = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new Exception("Target class [$className] does not exist.");
        }

        $bind = $this->binds[$className] ?? null;
        if ($bind) {
            return $bind($this);
        }

        $parameters = array_map(
            fn(ReflectionParameter $parameter) => $this->resolve($parameter->getType()->getName()),
            $reflector->getConstructor()?->getParameters() ?? []
        );
        return $reflector->newInstance(...$parameters);
    }

    /*
     * this function is used to register singleton
     */
    public function singleton(string $className, callable $callback): self
    {
        $this->binds[$className] = function () use ($callback, $className) {
            $instance = $callback($this);

            $this->singletons[$className] = $instance;

            return $instance;
        };
        return $this;
    }

    public static function instance(): self
    {
        return self::$instance;
    }

    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
    }
}