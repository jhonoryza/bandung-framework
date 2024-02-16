<?php

namespace Fajar\Bandung\Attribute;

use Attribute;
use ReflectionMethod;

#[Attribute]
class Command
{
    public ReflectionMethod $reflectionMethod;

    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    public function setReflectionMethod(ReflectionMethod $reflectionMethod): void
    {
        $this->reflectionMethod = $reflectionMethod;
    }

    public function __construct(public string $name)
    {
    }
}