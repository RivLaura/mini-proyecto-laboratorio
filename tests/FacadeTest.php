<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * La fachada se prueba como objeto real. Sus colaboradores se reemplazan por
 * dobles cuando se verifica la coordinacion, y se usan los reales (mediante
 * OrderServiceFactory) cuando se verifica la integracion con los patrones.
 */
final class FacadeTest extends TestCase
{
    /**
     * Arma la fachada con dobles que registran en $bitacora cada paso ejecutado.
     */
    private function crearFachadaConBitacora(array &$bitacora, float $total = 700.0): OrderService
    {
        $validator = $this->createMock(OrderValidator::class);
        $validator->method('validate')->willReturnCallback(
            function () use (&$bitacora): void {
                $bitacora[] = 'validar';
            }
        );

        $pricing = $this->createMock(OrderPricingService::class);
        $pricing->method('calculate')->willReturnCallback(
            function () use (&$bitacora, $total): float {
                $bitacora[] = 'calcular';

                return $total;
            }
        );

        $repository = $this->createMock(OrderRepository::class);
        $repository->method('save')->willReturnCallback(
            function () use (&$bitacora): void {
                $bitacora[] = 'guardar';
            }
        );

        $notifications = $this->createMock(OrderNotificationService::class);
        $notifications->method('notify')->willReturnCallback(
            function () use (&$bitacora): void {
                $bitacora[] = 'notificar';
            }
        );

        $events = $this->createMock(OrderEvents::class);
        $events->method('pedidoCreado')->willReturnCallback(
            function () use (&$bitacora): void {
                $bitacora[] = 'avisar';
            }
        );

        $reports = $this->createMock(OrderReportService::class);
        $reports->method('paraPedido')->willReturnCallback(
            function () use (&$bitacora): string {
                $bitacora[] = 'reporte';

                return 'Reporte de prueba';
            }
        );

        return new OrderService($validator, $pricing, $repository, $notifications, $events, $reports);
    }

    /**
     * Arma la fachada con dobles silenciosos, salvo los colaboradores reales
     * que se pasen por parametro.
     */
    private function crearFachada(
        ?OrderPricingService $pricing = null,
        ?OrderRepository $repository = null,
        ?OrderNotificationService $notifications = null,
        ?OrderEvents $events = null,
        ?OrderReportService $reports = null
    ): OrderService {
        if ($pricing === null) {
            $pricing = $this->createStub(OrderPricingService::class);
            $pricing->method('calculate')->willReturn(700.0);
        }

        if ($reports === null) {
            $reports = $this->createStub(OrderReportService::class);
            $reports->method('paraPedido')->willReturn('Reporte de prueba');
        }

        return new OrderService(
            new OrderValidator(),
            $pricing,
            $repository ?? $this->createStub(OrderRepository::class),
            $notifications ?? $this->createStub(OrderNotificationService::class),
            $events ?? $this->createStub(OrderEvents::class),
            $reports
        );
    }

    // ------------------------------------------------------------------
    // Coordinacion
    // ------------------------------------------------------------------

    public function testRespetaElOrdenDeEjecucionDeLosServicios(): void
    {
        $bitacora = [];
        $service = $this->crearFachadaConBitacora($bitacora);

        $service->procesarPedidoCompleto(1, 'Paciente', 1000.0, 'obra_social', 'email', 'a@b.com');

        $this->assertSame(
            ['validar', 'calcular', 'guardar', 'notificar', 'avisar', 'reporte'],
            $bitacora
        );
    }

    public function testValidacionCorrectaPermiteContinuar(): void
    {
        $service = $this->crearFachada();

        $resultado = $service->procesarPedidoCompleto(2, 'Paciente Valido', 1000.0, 'obra_social', 'ninguna', '');

        $this->assertInstanceOf(OrderResult::class, $resultado);
    }

    public static function pedidosInvalidos(): array
    {
        return [
            'paciente vacio' => ['', 1000.0, 'Falta el paciente'],
            'paciente con espacios' => ['   ', 1000.0, 'Falta el paciente'],
            'monto cero' => ['Paciente', 0.0, 'Monto invalido'],
            'monto negativo' => ['Paciente', -50.0, 'Monto invalido'],
        ];
    }

    #[DataProvider('pedidosInvalidos')]
    public function testRechazaPedidosInvalidos(string $paciente, float $monto, string $mensaje): void
    {
        $service = OrderServiceFactory::create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($mensaje);

        $service->procesarPedidoCompleto(3, $paciente, $monto, 'particular', 'email', 'a@b.com');
    }

    public function testValidacionFallidaNoProduceEfectosPosteriores(): void
    {
        $validator = $this->createMock(OrderValidator::class);
        $validator->method('validate')
            ->willThrowException(new InvalidArgumentException('Falta el paciente'));

        $pricing = $this->createMock(OrderPricingService::class);
        $pricing->expects($this->never())->method('calculate');

        $repository = $this->createMock(OrderRepository::class);
        $repository->expects($this->never())->method('save');

        $notifications = $this->createMock(OrderNotificationService::class);
        $notifications->expects($this->never())->method('notify');

        $events = $this->createMock(OrderEvents::class);
        $events->expects($this->never())->method('pedidoCreado');

        $reports = $this->createMock(OrderReportService::class);
        $reports->expects($this->never())->method('paraPedido');

        $service = new OrderService($validator, $pricing, $repository, $notifications, $events, $reports);

        $this->expectException(InvalidArgumentException::class);

        $service->procesarPedidoCompleto(4, '', 1000.0, 'particular', 'email', 'a@b.com');
    }

    public function testDevuelveUnOrderResultConPedidoTotalYReporte(): void
    {
        $bitacora = [];
        $service = $this->crearFachadaConBitacora($bitacora, 700.0);

        $resultado = $service->procesarPedidoCompleto(5, 'Paciente Resultado', 1000.0, 'obra_social', 'email', 'a@b.com');

        $this->assertInstanceOf(OrderResult::class, $resultado);
        $this->assertSame(5, $resultado->order->id);
        $this->assertSame('Paciente Resultado', $resultado->order->patient);
        $this->assertEqualsWithDelta(700.0, $resultado->total, 0.001);
        $this->assertSame('Reporte de prueba', $resultado->reporte);
    }

    // ------------------------------------------------------------------
    // Integracion con cada patron
    // ------------------------------------------------------------------

    public static function tiposDePaciente(): array
    {
        return [
            'particular' => ['particular', 1000.0],
            'obra social' => ['obra_social', 700.0],
            'jubilado' => ['jubilado', 500.0],
            'tipo desconocido cobra el monto completo' => ['otro', 1000.0],
        ];
    }

    #[DataProvider('tiposDePaciente')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testStrategyCalculaSegunElTipoDePaciente(string $tipoPaciente, float $esperado): void
    {
        $service = OrderServiceFactory::create();

        $this->expectOutputRegex('/\[DASHBOARD\] actualizado para el pedido 6/');

        $resultado = $service->procesarPedidoCompleto(6, 'Paciente Strategy', 1000.0, $tipoPaciente, 'ninguna', '');

        $this->assertEqualsWithDelta($esperado, $resultado->total, 0.001);
    }

    public function testRepositoryGuardaElPedidoConElImporteCalculado(): void
    {
        $repository = $this->createMock(OrderRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                fn(Order $order): bool => $order->id === 7
                    && $order->patient === 'Paciente Repository'
                    && abs($order->amount - 700.0) < 0.001
                    && $order->patientType === 'obra_social'
            ));

        $service = $this->crearFachada(repository: $repository);

        $service->procesarPedidoCompleto(7, 'Paciente Repository', 1000.0, 'obra_social', 'ninguna', '');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRepositoryRealPersisteMedianteConnection(): void
    {
        $conexion = Connection::obtener();
        $cantidadInicial = count($conexion->consultas());

        $this->expectOutputRegex('/\[DASHBOARD\] actualizado para el pedido 8/');

        OrderServiceFactory::create()
            ->procesarPedidoCompleto(8, 'Paciente Persistido', 2000.0, 'jubilado', 'ninguna', '');

        $consultas = $conexion->consultas();

        $this->assertCount($cantidadInicial + 1, $consultas);
        $this->assertStringContainsString(
            "VALUES (8, 'Paciente Persistido', 1000)",
            $consultas[array_key_last($consultas)]
        );
    }

    public function testFactoryEnviaLaNotificacionSolicitada(): void
    {
        $service = $this->crearFachada(notifications: new OrderNotificationService());

        $this->expectOutputString('[SMS] a 3704000000: Pedido 9 por $ 700<br>');

        $service->procesarPedidoCompleto(9, 'Paciente Factory', 1000.0, 'obra_social', 'sms', '3704000000');
    }

    public function testNotificacionNingunaNoAvisaAlPacientePeroSiALosObservadores(): void
    {
        $events = $this->createMock(OrderEvents::class);
        $events->expects($this->once())->method('pedidoCreado');

        $service = $this->crearFachada(
            notifications: new OrderNotificationService(),
            events: $events
        );

        $this->expectOutputString('');

        $service->procesarPedidoCompleto(10, 'Paciente Ninguna', 1000.0, 'obra_social', 'ninguna', '');
    }

    public function testTipoDeNotificacionDesconocidoLanzaExcepcion(): void
    {
        // La notificacion ocurre despues de guardar (validar -> calcular ->
        // guardar -> notificar): el pedido ya fue guardado cuando se informa
        // el error, y los pasos posteriores no se ejecutan.
        $repository = $this->createMock(OrderRepository::class);
        $repository->expects($this->once())->method('save');

        $events = $this->createMock(OrderEvents::class);
        $events->expects($this->never())->method('pedidoCreado');

        $service = $this->crearFachada(
            repository: $repository,
            notifications: new OrderNotificationService(),
            events: $events
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de notificacion no soportado: fax');

        $service->procesarPedidoCompleto(11, 'Paciente Fax', 1000.0, 'particular', 'fax', '123');
    }

    public function testObserverRecibeElPedidoCreado(): void
    {
        $events = $this->createMock(OrderEvents::class);
        $events->expects($this->once())
            ->method('pedidoCreado')
            ->with($this->callback(fn(Order $order): bool => $order->id === 12));

        $service = $this->crearFachada(events: $events);

        $service->procesarPedidoCompleto(12, 'Paciente Observer', 1000.0, 'obra_social', 'ninguna', '');
    }

    public function testObserverRealEjecutaLosObservadoresSuscriptos(): void
    {
        $events = new OrderEvents();
        $events->subscribe(new EmailObserver());
        $events->subscribe(new SmsObserver());
        $events->subscribe(new DashboardObserver());

        $service = $this->crearFachada(events: $events);

        $this->expectOutputString(
            'Email enviado para el pedido 13<br>'
            . 'SMS enviado para el pedido 13<br>'
            . '[DASHBOARD] actualizado para el pedido 13<br>'
        );

        $service->procesarPedidoCompleto(13, 'Paciente Observer', 1000.0, 'obra_social', 'ninguna', '');
    }

    public function testDecoratorGeneraElReporteCorrespondiente(): void
    {
        $service = $this->crearFachada(reports: new OrderReportService());

        $resultado = $service->procesarPedidoCompleto(14, 'Paciente Reporte', 1000.0, 'obra_social', 'ninguna', '');

        $this->assertSame('Reporte: Pedido 14 + firma digital + PDF', $resultado->reporte);
    }

    public function testLaFachadaNoImprimeLaPresentacion(): void
    {
        $service = $this->crearFachada(
            notifications: new OrderNotificationService(),
            reports: new OrderReportService()
        );

        // Sin notificacion ni observadores, la fachada no debe imprimir nada:
        // la presentacion queda a cargo de MVC.
        $this->expectOutputString('');

        $service->procesarPedidoCompleto(15, 'Paciente Vista', 1000.0, 'obra_social', 'ninguna', '');
    }
}
