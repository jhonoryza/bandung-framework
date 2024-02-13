<?php

namespace Tests;

use Fajar\Bandung\Container\Container;
use PHPUnit\Framework\TestCase;

class AppContainerTest extends TestCase
{

    /** @test */
    public function container_is_worked()
    {
        $container = new Container();

        $container->register(ContainerA::class, fn() => new ContainerA(new ContainerB()));

        $this->assertInstanceOf(ContainerA::class, $container->get(ContainerA::class));
    }

    /** @test */
    public function container_can_auto_resolved()
    {
        $container = new Container();

        $a = $container->get(ContainerA::class);

        $this->assertInstanceOf(ContainerA::class, $a);
    }

    /** @test */
    public function container_singleton_is_worked()
    {
        $container = new Container();

        $container->singleton(Singleton::class, fn () => new Singleton());

        $a = $container->get(Singleton::class);
        $this->assertEquals(1, $a::$count);

        $a = $container->get(Singleton::class);
        $this->assertEquals(1, $a::$count);
    }
}

class ContainerA
{
    public function __construct(public ContainerB $b){}
}

class ContainerB
{
}

class Singleton
{
    public static int $count = 0;

    public function __construct()
    {
        self::$count += 1;
    }
}
