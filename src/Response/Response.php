<?php

namespace Fajar\Bandung\Response;

use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Interface\ResponseInterface;
use Illuminate\Support\Collection;

class Response implements ResponseInterface
{
    protected string|null|array|Collection $body;

    private HttpHeader $status;

    private array $headers;

    public function __construct(HttpHeader $status, string|null|array|Collection $body = null, array $headers = [])
    {
        $this->status = $status;
        $this->body = $body;
        $this->headers = array_merge($this->getDefaultHeaders(), $headers);
    }

    protected function getDefaultHeaders(): array
    {
        return [
            'Content-Type' => 'text/html; charset=utf-8',
        ];
    }

    public static function make(HttpHeader $status, string|null|array|Collection $body = null, array $headers = []): self
    {
        return new static($status, $body, $headers);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->status->value;
    }

    public function notFound(): self
    {
        $this->status = HttpHeader::HTTP_404;
        $this->body = $this->body ?? '404 - not found';
        return $this;
    }
}