<?php

namespace Fajar\Bandung\Enum;

enum HttpHeader: int
{
    case HTTP_200 = 200;
    case HTTP_201 = 201;
    case HTTP_400 = 400;
    case HTTP_419 = 419;
    case HTTP_422 = 422;
    case HTTP_404 = 404;
    case HTTP_500 = 500;
}
