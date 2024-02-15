<?php

namespace Fajar\Bandung\Container;

use Exception;
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

    public function register(string $className, callable $callback): self
    {
        $this->binds[$className] = $callback;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function get(string $className): object
    {
        if ($instance = $this->singletons[$className] ?? null) {
            return $instance;
        }

        try {
            return $this->resolve($className); // the tree dots will convert to closure
        } catch (Throwable $throwable) {
            throw new Exception($throwable->getMessage());
        }
    }

    /**
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