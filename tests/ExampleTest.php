<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testLaInfraestructuraDePhpUnitFunciona(): void
    {
        $this->assertTrue(true);
    }

    public function testSePuedeInstanciarUnaClaseSimple(): void
    {
        $valor = 42;
        $this->assertSame(42, $valor);
    }
}
