<?php

namespace Fajar\Bandung\Container;

use Fajar\Bandung\Interface\Container;
use ReflectionClass;
use ReflectionParameter;
class AppContainer implements Container
{
    public array $binds = [];
    public array $singletons = [];

    public function register(string $className, callable $callback): self
    {
        $this->binds[$className] = $callback;

        return $this;
    }

    public function get(string $className): object
    {
        if ($instance = $this->singletons[$className] ?? null) {
            return $instance;
        }

        $bind = $this->binds[$className] ?? $this->resolve(...); // the tree dots will convert to closure

        return $bind($className);
    }

    /**
     * @throws \ReflectionException
     */
    private function resolve(string $className): object
    {
        $reflection = new ReflectionClass($className);

        $parameters = array_map(
            fn (ReflectionParameter $parameter) => $this->get($parameter->getType()->getName()),
            $reflection->getConstructor()?->getParameters() ?? []
        );

        return new $className(...$parameters);
    }

    public function singleton(string $className, callable $callback): self
    {
        $this->binds[$className] = function () use ($className, $callback) {
            $singleton = $callback($className);
            $this->singletons[$className] = $singleton;
            return $singleton;
        };
        return $this;
    }
}