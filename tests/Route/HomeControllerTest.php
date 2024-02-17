<?php

namespace Tests\Route;

use Fajar\Bandung\Enum\HttpHeader;
use Fajar\Bandung\Enum\RouteMethod;
use Tests\HttpTestCase;

class HomeControllerTest extends HttpTestCase
{
    /** @test */
    public function index_works()
    {
        $response = $this->getRequest('/');

        $this->assertEquals(HttpHeader::HTTP_200->value, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody());
    }

    /** @test */
    public function about_works()
    {
        $response = $this->getRequest('/about');

        $this->assertEquals(HttpHeader::HTTP_200->value, $response->getStatusCode());
        $this->assertEquals('about', $response->getBody());
    }

    /** @test */
    public function posts_works()
    {
        $response = $this->getRequest('/posts');

        $this->assertEquals(HttpHeader::HTTP_200->value, $response->getStatusCode());
        $this->json($response->getBody())->assertFragment(['message' => 'ok']);
    }

    /** @test */
    public function posts_detail_works()
    {
        $response = $this->getRequest('/posts/334');

        $this->assertEquals(HttpHeader::HTTP_200->value, $response->getStatusCode());
        $this->json($response->getBody())->assertFragment([
            'message' => 'ok',
            'id' => "334",
            'request' => RouteMethod::GET
        ]);
    }

    /** @test */
    public function json_works()
    {
        $response = $this->getRequest('/json');

        $this->assertEquals(HttpHeader::HTTP_200->value, $response->getStatusCode());
        $this->json($response->getBody())->assertFragment(['message' => 'ok']);
    }
}