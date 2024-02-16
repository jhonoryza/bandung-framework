<?php

namespace Fajar\Bandung\Interface;

interface ResponseInterface
{
    public function getHeaders(): array;

    public function getBody(): string;

    public function getStatusCode(): int;

    public function notFound(): self;

}