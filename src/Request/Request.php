<?php

namespace Fajar\Bandung\Request;

use Fajar\Bandung\Enum\RouteMethod;
use Fajar\Bandung\Interface\RequestInterface;

class Request implements RequestInterface
{
    private RouteMethod $method;
    private string $uri;
    private string $body;

    private string $path;
    private string $query;

    public function __construct(
        RouteMethod $method,
        string      $uri,
        string      $body
    )
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->body = $body;

        $decodedBody = rawurldecode($uri);
        $parse = parse_url($decodedBody);
        $this->path = $parse['path'] ?? '';
        $this->query = $parse['query'] ?? '';
    }

    public function getMethod(): RouteMethod
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}