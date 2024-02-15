<?php

namespace App\Controllers;

use Fajar\Bandung\Attribute\Get;
use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Response\Response;

class HomeController
{
    #[Get(uri: '/')]
    public function index(): Response
    {
        return Response::make(HttpHeader::HTTP_200, 'Hello world!');
    }
}