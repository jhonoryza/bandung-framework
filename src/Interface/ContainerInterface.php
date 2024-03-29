<?php

namespace Fajar\Bandung\Interface;

interface ContainerInterface
{
    public function register(string $className, callable $callback): self;

    public function singleton(string $className, callable $callback): self;

    public function get(string $className): object;
}