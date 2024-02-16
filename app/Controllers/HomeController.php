<?php

namespace App\Controllers;

use Fajar\Bandung\Attribute\Get;
use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Interface\RequestInterface;
use Fajar\Bandung\Interface\ResponseInterface;
use Fajar\Bandung\Response\JsonResponse;
use Fajar\Bandung\Response\Response;

class HomeController
{
    #[Get(uri: '/')]
    public function index(): ResponseInterface
    {
        return Response::make(HttpHeader::HTTP_200, 'Hello world!');
    }

    #[Get(uri: '/about')]
    public function about(): ResponseInterface
    {
        return Response::make(HttpHeader::HTTP_200, 'about');
    }

    #[Get(uri: '/posts')]
    public function posts(): ResponseInterface
    {
        return JsonResponse::make(HttpHeader::HTTP_200, [
            'message' => 'ok'
        ]);
    }

    #[Get(uri: '/posts/{id}')]
    public function postDetail(RequestInterface $request, string $id): ResponseInterface
    {
        return JsonResponse::make(HttpHeader::HTTP_200, [
            'message' => 'ok',
            'id' => $id,
            'request' => $request->getMethod()
        ]);
    }

    #[Get(uri: '/json')]
    public function json(): ResponseInterface
    {
        return jsonResponse([
            'message' => 'ok'
        ]);
    }
}
