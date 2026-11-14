<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class RoutesTest extends TestCase
{
    private function ejecutarRuta(string $accion): void
    {
        $_GET = ['accion' => $accion];

        require __DIR__ . '/../public/index.php';
    }

    public function testCrearPedido(): void
    {
        $this->expectOutputRegex('/<h1>Pedido creado<\/h1>/');

        $this->ejecutarRuta('crear');
    }

    public function testListarPedidos(): void
    {
        $this->expectOutputRegex('/<h1>Pedidos<\/h1>/');

        $this->ejecutarRuta('listar');
    }

    public function testGenerarReporte(): void
    {
        $this->expectOutputString(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua'
        );

        $this->ejecutarRuta('reporte');
    }

    public function testAccionInexistente(): void
    {
        $this->expectOutputString('Accion no encontrada');

        $this->ejecutarRuta('inexistente');

        $this->assertSame(404, http_response_code());
    }
}
