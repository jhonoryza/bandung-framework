<?php

namespace Fajar\Bandung\Interface;

use Fajar\Bandung\Enum\RouteMethod;

interface RequestInterface
{
    public function getMethod(): RouteMethod;

    public function getUri(): string;

    public function getBody(): string;

    public function getPath(): string;

    public function getQuery(): ?string;

}