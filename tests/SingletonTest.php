<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

final class SingletonTest extends TestCase
{
    public function testObtenerDevuelveSiempreLaMismaInstancia(): void
    {
        $conexion1 = Connection::obtener();
        $conexion2 = Connection::obtener();

        $this->assertSame($conexion1, $conexion2);
    }

    public function testConstructorEsPrivado(): void
    {
        $reflexion = new ReflectionClass(Connection::class);

        $this->assertTrue(
            $reflexion->getConstructor()->isPrivate()
        );
    }

    public function testNoPermiteClonarLaConexion(): void
    {
        $conexion = Connection::obtener();

        $this->expectException(Error::class);

        clone $conexion;
    }

    public function testNoPermiteDeserializarLaConexion(): void
    {
        $conexion = Connection::obtener();
        $serializada = serialize($conexion);

        $this->expectException(LogicException::class);

        unserialize($serializada);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testConservaLasConsultasEnLaInstanciaCompartida(): void
    {
        $conexion1 = Connection::obtener();
        $conexion2 = Connection::obtener();

        $consultasAnteriores = $conexion1->consultas();

        $sql = "INSERT INTO orders VALUES (999, 'Test Singleton', 100)";

        $conexion1->ejecutar($sql);

        $this->assertSame(
            [...$consultasAnteriores, $sql],
            $conexion2->consultas()
        );
    }
}
