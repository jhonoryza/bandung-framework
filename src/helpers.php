<?php

use Fajar\Bandung\Container\Container;
use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Response\JsonResponse;
use Fajar\Bandung\Response\Response;
use Illuminate\Support\Collection;

function app(): Container
{
    return Container::instance();
}

function response(string|null $message = null): ResponseInterface
{
    return Response::make(HttpHeader::HTTP_200, $message);
}

function jsonResponse(string|null|array|Collection $message = null): ResponseInterface
{
    return JsonResponse::make(HttpHeader::HTTP_200, $message);
}

function request(): RequestInterface
{
    return app()->get(RequestInterface::class);
}