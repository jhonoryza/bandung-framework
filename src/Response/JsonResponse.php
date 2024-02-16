<?php

namespace Fajar\Bandung\Response;

class JsonResponse extends Response
{
    protected function getDefaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
        ];
    }

    public function getBody(): string
    {
        return match (gettype($this->body)) {
            'NULL' => collect('')->toJson(),
            'string', 'array' => collect($this->body)->toJson(),
            default => $this->body->toJson()
        };
    }
}