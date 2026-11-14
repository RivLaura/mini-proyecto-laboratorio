<?php
/**
 * ============================================================================
 *  CONTROLADOR DE PEDIDOS
 *  Patron esperado: MVC bien aplicado (arquitectonico) + SRP
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA — el "God Controller"
 *     1. Arma SQL directamente.
 *     2. Contiene reglas de negocio (descuentos).
 *     3. Imprime HTML con echo.
 *     4. Crea con new todas sus dependencias concretas.
 *     5. Repite por TERCERA vez el if de notificaciones.
 *
 *  Tener las carpetas /Controllers /Models /views NO significa aplicar MVC.
 *  MVC es separacion de responsabilidades, no estructura de directorios.
 *
 *  ✅ FORMA CORRECTA
 *     El controlador solo: recibe la entrada, la valida como entrada,
 *     llama a UN servicio o fachada, y elige que vista renderizar.
 * ============================================================================
 */

class OrderController
{
    /**
     * ✅ METODO APLICADO: create()
     *    Recibe la entrada HTTP, delega el procesamiento al Facade
     *    y selecciona la vista. Maximo 5 lineas ejecutables.
     *    Sin SQL, sin reglas de negocio, sin echo.
     *    Captura excepciones de validacion para presentar una respuesta controlada.
     */
    public function create(): void
    {
        $entrada = $this->leerEntradaHttp();

        try {
            $resultado = OrderServiceFactory::create()->procesarPedidoCompleto(
                $entrada['id'],
                $entrada['paciente'],
                $entrada['monto'],
                $entrada['tipoPaciente'],
                $entrada['tipoNotificacion'],
                $entrada['destino']
            );

            require __DIR__ . '/../../views/pedido-creado.php';
        } catch (InvalidArgumentException $e) {
            $error = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            require __DIR__ . '/../../views/error.php';
        }
    }

    /**
     * Lee y normaliza los parametros de entrada HTTP con valores por defecto.
     * No contiene reglas de negocio: solo extrae y castea los valores del request.
     */
    private function leerEntradaHttp(): array
    {
        return [
            'id'             => (int) ($_GET['id'] ?? 1),
            'paciente'       => $_GET['paciente'] ?? 'Juan Perez',
            'monto'          => (float) ($_GET['monto'] ?? 15000),
            'tipoPaciente'   => $_GET['tipo'] ?? 'obra_social',
            'tipoNotificacion' => $_GET['notificacion'] ?? 'email',
            'destino'        => $_GET['destino'] ?? 'paciente@mail.com',
        ];
    }

    /**
     * ✅ METODO APLICADO: index()
     *    Prepara los datos del listado y los pasa a la vista.
     *    La vista no consulta la base de datos ni calcula descuentos.
     *
     *    Nota: este listado usa datos de ejemplo preparados en el controlador.
     *    No representa un listado real de pedidos guardados, porque la persistencia
     *    actual es una simulación en memoria (Connection) sin base de datos real
     *    configurada y no persiste entre solicitudes HTTP independientes.
     */
    public function index(): void
    {
        // Datos de ejemplo para la demostracion academica con persistencia simulada.
        // No consultar BD ni asumir persistencia entre requests HTTP independientes.
        $pedidos = [
            ['id' => 1, 'paciente' => 'Juan Perez', 'total' => 10500, 'tipo' => 'obra_social'],
            ['id' => 2, 'paciente' => 'Ana Gomez',   'total' => 22000, 'tipo' => 'particular'],
        ];

        require __DIR__ . '/../../views/orders.php';
    }

    /**
     * ✅ METODO APLICADO: report()
     *    Genera el reporte mediante composicion de decoradores
     *    y delega la presentacion a la vista.
     *    Sin echo directo.
     */
    public function report(): void
    {
        $reporte = new WatermarkDecorator(
            new PdfReportDecorator(
                new DigitalSignatureDecorator(
                    new BasicReport('Pedidos del dia')
                )
            )
        )->generate();

        require __DIR__ . '/../../views/reporte.php';
    }
}

/*
 * ----------------------------------------------------------------------------
 * EJERCICIO 7 (TP): dejar create() con un maximo de 5 lineas ejecutables,
 * sin SQL, sin reglas de negocio y sin echo.
 *
 * PREGUNTA PARA EL PR: cuales de los 5 problemas de este archivo son deuda
 * de DISEÑO (Unidad 2) y cuales son deuda de PROCESO (Unidad 1)?
 * ----------------------------------------------------------------------------
 */
