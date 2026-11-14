<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

final class RepositoryTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGuardarPedidoRegistraLaConsulta(): void
    {
        $conexion = Connection::obtener();
        $cantidadInicial = count($conexion->consultas());

        $pedido = new Order(101, 'Paciente Test', 1500.0, 'particular');
        $repository = new OrderRepository();

        $repository->save($pedido);

        $consultas = $conexion->consultas();

        $this->assertCount($cantidadInicial + 1, $consultas);
        $this->assertStringContainsString(
            "VALUES (101, 'Paciente Test', 1500)",
            $consultas[array_key_last($consultas)]
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGuardarDosPedidosRegistraAmbasConsultas(): void
    {
        $conexion = Connection::obtener();
        $cantidadInicial = count($conexion->consultas());

        $repository = new OrderRepository();

        $repository->save(
            new Order(201, 'Paciente Uno', 1000.0, 'particular')
        );

        $repository->save(
            new Order(202, 'Paciente Dos', 2000.0, 'obra_social')
        );

        $consultas = $conexion->consultas();

        $this->assertCount($cantidadInicial + 2, $consultas);

        $this->assertStringContainsString(
            "'Paciente Uno'",
            $consultas[$cantidadInicial]
        );

        $this->assertStringContainsString(
            "'Paciente Dos'",
            $consultas[$cantidadInicial + 1]
        );
    }

    public function testModeloOrderNoTieneMetodoGuardar(): void
    {
        $this->assertFalse(
            method_exists(Order::class, 'guardar')
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testOrderServiceConservaLaPersistencia(): void
    {
        $conexion = Connection::obtener();
        $cantidadInicial = count($conexion->consultas());

        $service = new OrderService();

        $this->expectOutputRegex('/Pedido 303 procesado/');

        $service->procesarPedidoCompleto(
            303,
            'Paciente Integracion',
            1000.0,
            'obra_social',
            'ninguna',
            ''
        );

        $consultas = $conexion->consultas();

        $this->assertCount($cantidadInicial + 1, $consultas);

        $this->assertStringContainsString(
            "VALUES (303, 'Paciente Integracion', 700)",
            $consultas[array_key_last($consultas)]
        );
    }
}
