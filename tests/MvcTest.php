<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

/**
 * Pruebas de integración MVC.
 *
 * Comprueban que el controlador coordina mediante Facade, que las vistas
 * solo presentan y escapan, y que ningún patrón integrado se duplica ni
 * se elimina.
 *
 * Se ejecutan en procesos separados porque requieren modificar $_GET e
 * incluir public/index.php (que lee $_GET y puede modificar el estado
 * global de salida y códigos de HTTP).
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class MvcTest extends TestCase
{
    private function ejecutarRuta(string $accion, array $params = []): void
    {
        $_GET = array_merge(['accion' => $accion], $params);

        require __DIR__ . '/../public/index.php';
    }

    // ------------------------------------------------------------------
    // 1. La ruta crear utiliza el procesamiento mediante Facade.
    // ------------------------------------------------------------------
    public function testRutaCrearUtilizaFacade(): void
    {
        $reflection = new ReflectionClass(OrderController::class);
        $metodo     = $reflection->getMethod('create');
        $codigo     = file_get_contents($metodo->getFileName());

        $this->assertStringContainsString(
            'OrderServiceFactory::create()',
            $codigo,
            'create() debe delegar en OrderServiceFactory (Facade)'
        );
        $this->assertStringContainsString(
            'procesarPedidoCompleto(',
            $codigo,
            'create() debe invocar procesarPedidoCompleto del Facade'
        );
    }

    // ------------------------------------------------------------------
    // 2. El resultado muestra el paciente y el total correcto.
    // ------------------------------------------------------------------
    public function testResultadoMuestraPacienteYTotal(): void
    {
        ob_start();

        $this->ejecutarRuta('crear', [
            'id'             => 10,
            'paciente'       => 'Maria Garcia',
            'monto'          => 20000,
            'tipo'           => 'obra_social',
            'notificacion'   => 'ninguna',
            'destino'        => '',
        ]);

        $salida = ob_get_clean();

        $this->assertStringContainsString('Maria Garcia', $salida);
        // obra_social: 20000 * 0.7 = 14000 (calculado por Strategy, no por el controlador)
        $this->assertStringContainsString('14000', $salida);
    }

    // ------------------------------------------------------------------
    // 3. El controlador no contiene SQL directo.
    // ------------------------------------------------------------------
    public function testControladorNoContieneSqlDirecto(): void
    {
        $reflection = new ReflectionClass(OrderController::class);
        $metodo     = $reflection->getMethod('create');
        $codigo     = file_get_contents($metodo->getFileName());

        $this->assertStringNotContainsString('Connection::obtener()', $codigo);
        $this->assertStringNotContainsString('INSERT INTO', $codigo);
        $this->assertStringNotContainsString('SELECT ', $codigo);
        $this->assertStringNotContainsString('->ejecutar(', $codigo);
    }

    // ------------------------------------------------------------------
    // 4. El controlador no calcula descuentos.
    // ------------------------------------------------------------------
    public function testControladorNoCalculaDescuentos(): void
    {
        $reflection = new ReflectionClass(OrderController::class);
        $metodo     = $reflection->getMethod('create');
        $codigo     = file_get_contents($metodo->getFileName());

        $this->assertStringNotContainsString('0.7', $codigo);
        $this->assertStringNotContainsString('0.5', $codigo);
        $this->assertStringNotContainsString('calcularTotal', $codigo);
    }

    // ------------------------------------------------------------------
    // 5. La ruta listar recibe datos preparados.
    // ------------------------------------------------------------------
    public function testRutaListarRecibeDatosPreparados(): void
    {
        $reflection = new ReflectionClass(OrderController::class);
        $metodo     = $reflection->getMethod('index');
        $codigo     = file_get_contents($metodo->getFileName());

        $this->assertStringContainsString('$pedidos = [', $codigo);
        $this->assertStringContainsString("require __DIR__ . '/../../views/orders.php'", $codigo);
    }

    // ------------------------------------------------------------------
    // 6. La vista no consulta la base de datos.
    // ------------------------------------------------------------------
    public function testVistaNoConsultaBaseDeDatos(): void
    {
        $codigo = file_get_contents(__DIR__ . '/../views/orders.php');

        $this->assertStringNotContainsString('Connection::obtener()', $codigo);
        $this->assertStringNotContainsString('->ejecutar(', $codigo);
        $this->assertStringNotContainsString('->consultas()', $codigo);
    }

    // ------------------------------------------------------------------
    // 7. La vista no calcula precios.
    // ------------------------------------------------------------------
    public function testVistaNoCalculaPrecios(): void
    {
        $codigo = file_get_contents(__DIR__ . '/../views/orders.php');

        $this->assertStringNotContainsString('0.7', $codigo);
        $this->assertStringNotContainsString('0.5', $codigo);
        $this->assertStringNotContainsString('calcularTotal', $codigo);
        $this->assertStringNotContainsString('monto *', $codigo);
        $this->assertStringNotContainsString('total *', $codigo);
    }

    // ------------------------------------------------------------------
    // 8. Los datos del paciente se muestran escapados (XSS).
    // ------------------------------------------------------------------
    public function testDatosPacienteEscapados(): void
    {
        $codigo = file_get_contents(__DIR__ . '/../views/pedido-creado.php');

        $this->assertStringContainsString('htmlspecialchars', $codigo);
        $this->assertStringContainsString('ENT_QUOTES', $codigo);
        $this->assertStringContainsString('UTF-8', $codigo);
    }

    // 8b. Escape de caracteres especiales en la vista de listado.
    public function testEscapeCaracteresEspecialesListado(): void
    {
        $codigo = file_get_contents(__DIR__ . '/../views/orders.php');

        $this->assertStringContainsString('htmlspecialchars', $codigo);
    }

    // 8c. Prueba de integración: HTML inyectado en el paciente se escapa.
    public function testEscapeHtmlEnNombrePaciente(): void
    {
        ob_start();

        $this->ejecutarRuta('crear', [
            'id'             => 1,
            'paciente'       => '<script>alert(1)</script>',
            'monto'          => 1000,
            'tipo'           => 'particular',
            'notificacion'   => 'ninguna',
            'destino'        => '',
        ]);

        $salida = ob_get_clean();

        $this->assertStringNotContainsString('<script>', $salida);
        $this->assertStringContainsString('&lt;script&gt;', $salida);
    }

    // ------------------------------------------------------------------
    // 9. La ruta reporte conserva Decorator.
    // ------------------------------------------------------------------
    public function testRutaReporteConservaDecorator(): void
    {
        ob_start();

        $this->ejecutarRuta('reporte');

        $salida = ob_get_clean();

        $this->assertStringContainsString(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
            $salida
        );
    }

    // ------------------------------------------------------------------
    // 10. Una acción inexistente devuelve HTTP 404.
    // ------------------------------------------------------------------
    public function testAccionInexistenteDevuelve404(): void
    {
        ob_start();
        $this->ejecutarRuta('inexistente');
        ob_get_clean();

        $this->assertSame(404, http_response_code());
    }

    // ------------------------------------------------------------------
    // 11. Los datos inválidos producen una respuesta controlada.
    // ------------------------------------------------------------------
    public function testDatosInvalidosProducenRespuestaControlada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Falta el paciente');

        $this->ejecutarRuta('crear', [
            'id'             => 1,
            'paciente'       => '',
            'monto'          => 100,
            'tipo'           => 'particular',
            'notificacion'   => 'ninguna',
            'destino'        => '',
        ]);
    }

    // ------------------------------------------------------------------
    // 12. Repository y Strategy continúan funcionando.
    // ------------------------------------------------------------------
    public function testRepositoryYStrategyContinúanFuncionando(): void
    {
        // Repository: guardar en la simulación en memoria no lanza excepción
        $repository = new OrderRepository();
        $order      = new Order(1, 'Test Paciente', 1000, 'particular');

        try {
            $repository->save($order);
        } catch (Throwable $e) {
            $this->fail('OrderRepository::save lanzó una excepción inesperada: ' . $e->getMessage());
        }

        // Strategy vía OrderPricingService
        $strategies = [
            'particular'  => new PrivatePatientStrategy(),
            'obra_social' => new InsuranceStrategy(),
            'jubilado'    => new RetiredPatientStrategy(),
        ];
        $pricing = new OrderPricingService($strategies, new PrivatePatientStrategy());

        $this->assertSame(1000.0, $pricing->calculate(1000, 'particular'));
        $this->assertSame(700.0,  $pricing->calculate(1000, 'obra_social'));
        $this->assertSame(500.0,  $pricing->calculate(1000, 'jubilado'));
    }

    // ------------------------------------------------------------------
    // 13. Factory y Observer conservan las notificaciones.
    // ------------------------------------------------------------------
    public function testFactoryYObserverConservanNotificaciones(): void
    {
        // Factory: crea notificadores concretos sin if en el llamador
        $email = NotificationFactory::create('email', 'test@mail.com');
        $this->assertInstanceOf(EmailNotification::class, $email);

        $sms = NotificationFactory::create('sms', '1234567890');
        $this->assertInstanceOf(SmsNotification::class, $sms);

        // Observer: los observadores se notifican sin cortar la cadena ante fallos
        ob_start();
        $eventos = new OrderEvents();
        try {
            $eventos->subscribe(new EmailObserver());
            $eventos->subscribe(new DashboardObserver());
            $eventos->pedidoCreado(new Order(1, 'Test', 1000, 'particular'));
        } catch (Throwable $e) {
            ob_get_clean();
            $this->fail('OrderEvents::pedidoCreado lanzó una excepción inesperada: ' . $e->getMessage());
        }
        ob_get_clean();
    }

    // ------------------------------------------------------------------
    // 14. Facade devuelve correctamente OrderResult.
    // ------------------------------------------------------------------
    public function testFacadeDevuelveOrderResult(): void
    {
        ob_start();
        $service = OrderServiceFactory::create();
        $resultado = $service->procesarPedidoCompleto(
            1,
            'Paciente Test',
            10000,
            'obra_social',
            'ninguna',
            ''
        );
        ob_get_clean();

        $this->assertInstanceOf(OrderResult::class, $resultado);
        $this->assertInstanceOf(Order::class, $resultado->order);
        $this->assertSame('Paciente Test', $resultado->order->patient);
        // 10000 * 0.7 = 7000 (Strategy: InsuranceStrategy con 30% de descuento)
        $this->assertSame(7000.0, $resultado->total);
        $this->assertIsString($resultado->reporte);
    }
}
