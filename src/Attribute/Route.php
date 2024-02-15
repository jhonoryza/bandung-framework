<?php

namespace Fajar\Bandung\Attribute;

use Attribute;
use Fajar\Bandung\Enum\RouteMethod;
use Fajar\Bandung\Interface\RouteInterface;
use ReflectionMethod;

#[Attribute]
class Route implements RouteInterface
{
    public ReflectionMethod $reflectionMethod;

    public function __construct(public RouteMethod $method, public string $uri)
    {
    }

    public function setReflectionMethod($reflectionMethod): void
    {
        $this->reflectionMethod = $reflectionMethod;
    }
}
