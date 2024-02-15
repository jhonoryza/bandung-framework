<?php

namespace Fajar\Bandung\Interface;


use Fajar\Bandung\Attribute\Route;

interface RouterInterface
{
    public function addRoute(Route $route): self;

    public function dispatch(RequestInterface $request): ResponseInterface;
}