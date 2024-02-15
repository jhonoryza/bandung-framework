<?php

namespace Fajar\Bandung\Interface;

use Fajar\Bandung\Enum\HttpHeader;

interface ResponseInterface
{
    public function getHeaders(): array;

    public function getBody(): string;

    public function getStatus(): HttpHeader;

}