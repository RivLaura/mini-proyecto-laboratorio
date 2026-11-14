<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ObserverTest extends TestCase
{
    private function crearPedido(int $id = 501): Order
    {
        return new Order($id, 'Paciente Observer', 1000.0, 'particular');
    }

    /**
     * Observador de prueba que registra su nombre en una bitacora compartida
     * para verificar quien recibio el evento y en que orden.
     */
    private function crearObservadorRegistrador(string $nombre, array &$bitacora): OrderObserver
    {
        return new class ($nombre, $bitacora) implements OrderObserver {
            private array $bitacora;

            public function __construct(private string $nombre, array &$bitacora)
            {
                $this->bitacora = &$bitacora;
            }

            public function update(Order $order): void
            {
                $this->bitacora[] = "{$this->nombre}:{$order->id}";
            }
        };
    }

    private function crearObservadorQueFalla(string $mensaje): OrderObserver
    {
        return new class ($mensaje) implements OrderObserver {
            public function __construct(private string $mensaje)
            {
            }

            public function update(Order $order): void
            {
                throw new RuntimeException($this->mensaje);
            }
        };
    }

    public function testSinSuscriptoresNoGeneraErroresNiNotificaciones(): void
    {
        $eventos = new OrderEvents();

        $this->expectOutputString('');

        $eventos->pedidoCreado($this->crearPedido());
    }

    public function testLosTresObservadoresRecibenElEvento(): void
    {
        $eventos = new OrderEvents();
        $eventos->subscribe(new EmailObserver());
        $eventos->subscribe(new SmsObserver());
        $eventos->subscribe(new DashboardObserver());

        $this->expectOutputString(
            'Email enviado para el pedido 501<br>'
            . 'SMS enviado para el pedido 501<br>'
            . '[DASHBOARD] actualizado para el pedido 501<br>'
        );

        $eventos->pedidoCreado($this->crearPedido());
    }

    public function testLosObservadoresSeEjecutanEnElOrdenDeSuscripcion(): void
    {
        $bitacora = [];
        $eventos = new OrderEvents();
        $eventos->subscribe($this->crearObservadorRegistrador('tercero', $bitacora));
        $eventos->subscribe($this->crearObservadorRegistrador('primero', $bitacora));
        $eventos->subscribe($this->crearObservadorRegistrador('segundo', $bitacora));

        $eventos->pedidoCreado($this->crearPedido(77));

        $this->assertSame(['tercero:77', 'primero:77', 'segundo:77'], $bitacora);
    }

    public function testCadaEventoNotificaUnaVezPorObservador(): void
    {
        $bitacora = [];
        $eventos = new OrderEvents();
        $eventos->subscribe($this->crearObservadorRegistrador('auditoria', $bitacora));

        $eventos->pedidoCreado($this->crearPedido(1));
        $eventos->pedidoCreado($this->crearPedido(2));

        $this->assertSame(['auditoria:1', 'auditoria:2'], $bitacora);
    }

    public function testSePuedeAgregarUnObservadorNuevoSinModificarOrderEvents(): void
    {
        $observadorWhatsApp = new class implements OrderObserver {
            public ?int $pedidoRecibido = null;

            public function update(Order $order): void
            {
                $this->pedidoRecibido = $order->id;
            }
        };

        $eventos = new OrderEvents();
        $eventos->subscribe($observadorWhatsApp);

        $eventos->pedidoCreado($this->crearPedido(808));

        $this->assertSame(808, $observadorWhatsApp->pedidoRecibido);
    }

    public function testSiUnObservadorFallaLosRestantesSiguenEjecutandose(): void
    {
        $bitacora = [];
        $eventos = new OrderEvents();
        $eventos->subscribe($this->crearObservadorRegistrador('email', $bitacora));
        $eventos->subscribe($this->crearObservadorQueFalla('SMS caido'));
        $eventos->subscribe($this->crearObservadorRegistrador('dashboard', $bitacora));

        try {
            $eventos->pedidoCreado($this->crearPedido(42));
            $this->fail('Se esperaba una excepcion por el observador que fallo.');
        } catch (RuntimeException $error) {
            $this->assertSame(
                'Fallaron 1 observador(es) del pedido 42: SMS caido',
                $error->getMessage()
            );
            $this->assertInstanceOf(RuntimeException::class, $error->getPrevious());
            $this->assertSame('SMS caido', $error->getPrevious()->getMessage());
        }

        $this->assertSame(['email:42', 'dashboard:42'], $bitacora);
    }

    public function testSiVariosObservadoresFallanSeInformanTodosLosErrores(): void
    {
        $bitacora = [];
        $eventos = new OrderEvents();
        $eventos->subscribe($this->crearObservadorQueFalla('Email caido'));
        $eventos->subscribe($this->crearObservadorRegistrador('dashboard', $bitacora));
        $eventos->subscribe($this->crearObservadorQueFalla('SMS caido'));

        try {
            $eventos->pedidoCreado($this->crearPedido(43));
            $this->fail('Se esperaba una excepcion por los observadores que fallaron.');
        } catch (RuntimeException $error) {
            $this->assertSame(
                'Fallaron 2 observador(es) del pedido 43: Email caido; SMS caido',
                $error->getMessage()
            );
            // La primera falla queda encadenada como causa original.
            $this->assertSame('Email caido', $error->getPrevious()->getMessage());
        }

        $this->assertSame(['dashboard:43'], $bitacora);
    }

    public static function suscripcionesInvalidas(): array
    {
        return [
            'objeto generico' => [new stdClass()],
            'notificacion de Factory' => [new SmsNotification('3704000000')],
        ];
    }

    #[DataProvider('suscripcionesInvalidas')]
    public function testRechazaObjetosQueNoImplementanOrderObserver(object $invalido): void
    {
        $eventos = new OrderEvents();

        $this->expectException(TypeError::class);

        $eventos->subscribe($invalido);
    }
}
