<?php

namespace Fajar\Bandung\Attribute;

use Attribute;
use Fajar\Bandung\Enum\RouteMethod;

#[Attribute]
class Get extends Route
{
    public function __construct(string $uri)
    {
        parent::__construct(RouteMethod::GET, $uri);
    }
}