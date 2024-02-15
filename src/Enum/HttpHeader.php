<?php

namespace Fajar\Bandung\Enum;

enum HttpHeader: string
{
    case HTTP_200 = '200 OK';
    case HTTP_404 = '404 Not Found';
    case HTTP_500 = '500 Internal Server Error';
}
