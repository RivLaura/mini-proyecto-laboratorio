<?php
/**
 * ============================================================================
 *  SERVICIO DE PEDIDOS
 *  Patron aplicado: FACADE (estructural) + servicios cohesivos (SRP)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     procesarPedidoCompleto() validaba, calculaba descuentos (0.7 / 0.5),
 *     persistia, repetia el if de notificaciones, creaba los avisos, armaba
 *     el reporte con banderas booleanas e imprimia HTML. Todo con new
 *     adentro: imposible de probar sin base de datos ni servidor de mail.
 *
 *  SOLUCION
 *     OrderService es una FACHADA: solo COORDINA. Cada paso lo resuelve el
 *     componente que ya existe en el proyecto, inyectado por constructor:
 *
 *       Validacion     -> OrderValidator
 *       Precio         -> OrderPricingService -> PriceCalculator + PricingStrategy (Strategy, #14)
 *       Persistencia   -> OrderRepository (Repository, #13)
 *       Notificacion   -> OrderNotificationService -> NotificationFactory (Factory, #15)
 *       Avisos         -> OrderEvents + OrderObserver (Observer)
 *       Reporte        -> OrderReportService -> Report + decoradores (Decorator)
 *
 *     La fachada no contiene porcentajes, SQL, if por tipo de notificacion
 *     ni HTML: devuelve un OrderResult y la presentacion queda en MVC.
 *     El armado de todas las piezas esta en OrderServiceFactory.
 *
 *     EJERCICIO 6: procesarPedidoCompleto() tiene 8 lineas, sin perder
 *     ninguno de los pasos del metodo original.
 *
 *  CONSECUENCIA NEGATIVA
 *     La complejidad no desaparece: se reparte entre mas clases y el
 *     constructor recibe seis dependencias. Para entender el flujo completo
 *     hay que recorrer varios archivos, y la fachada puede volver a crecer
 *     si alguien empieza a poner reglas de negocio aca ("una fachada
 *     coordina; si decide, volvimos al punto de partida").
 * ============================================================================
 */

final class OrderService
{
    public function __construct(
        private OrderValidator $validator,
        private OrderPricingService $pricing,
        private OrderRepository $repository,
        private OrderNotificationService $notifications,
        private OrderEvents $events,
        private OrderReportService $reports
    ) {
    }

    /**
     * @throws InvalidArgumentException si el pedido no es valido o el tipo
     *                                  de notificacion no esta soportado.
     */
    public function procesarPedidoCompleto(
        int $id,
        string $paciente,
        float $monto,
        string $tipoPaciente,
        string $tipoNotificacion,
        string $destino
    ): OrderResult {
        $this->validator->validate($paciente, $monto);
        $total = $this->pricing->calculate($monto, $tipoPaciente);
        $order = new Order($id, $paciente, $total, $tipoPaciente);
        $this->repository->save($order);
        $this->notifications->notify($order, $tipoNotificacion, $destino);
        $this->events->pedidoCreado($order);
        $reporte = $this->reports->paraPedido($order);
        return new OrderResult($order, $total, $reporte);
    }
}
