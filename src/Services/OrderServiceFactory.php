<?php
/**
 * Arma un OrderService con la configuracion por defecto del laboratorio.
 *
 * Es el UNICO lugar donde se eligen las implementaciones concretas:
 *   - que estrategia de precio corresponde a cada tipo de paciente,
 *   - que observadores se enteran de un pedido creado.
 *
 * Asi la fachada y sus servicios reciben todo por constructor y se pueden
 * probar con dobles. Tambien compensa la consecuencia negativa de Observer:
 * para saber quien recibe el evento, se mira esta clase.
 *
 * Uso (por ejemplo, desde el controlador):
 *   $resultado = OrderServiceFactory::create()
 *       ->procesarPedidoCompleto(1, 'Juan Perez', 15000, 'obra_social', 'email', 'paciente@mail.com');
 */
final class OrderServiceFactory
{
    public static function create(): OrderService
    {
        $events = new OrderEvents();
        $events->subscribe(new EmailObserver());
        $events->subscribe(new SmsObserver());
        $events->subscribe(new DashboardObserver());

        return new OrderService(
            new OrderValidator(),
            new OrderPricingService(
                [
                    'particular'  => new PrivatePatientStrategy(),
                    'obra_social' => new InsuranceStrategy(),
                    'jubilado'    => new RetiredPatientStrategy(),
                ],
                new PrivatePatientStrategy()
            ),
            new OrderRepository(),
            new OrderNotificationService(),
            $events,
            new OrderReportService()
        );
    }
}
