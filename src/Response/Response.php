<?php

namespace Fajar\Bandung\Response;

use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Interface\ResponseInterface;

class Response implements ResponseInterface
{
    private string $body;

    private HttpHeader $status;

    private array $headers;

    public function __construct(HttpHeader $status, string|null $body = null, array $headers = [])
    {
        $this->status = $status;
        $this->body = $body ?? '';
        $this->headers = $headers;
    }

    public static function make(HttpHeader $status, string|null $body = null, array $headers = []): self
    {
        return new self($status, $body, $headers);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): HttpHeader
    {
        return $this->status;
    }
}