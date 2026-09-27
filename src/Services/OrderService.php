<?php
/**
 * ============================================================================
 *  SERVICIO DE PEDIDOS
 *  Patron aplicado: FACADE (estructural) + separacion en servicios cohesivos
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, procesarPedidoCompleto() concentraba validacion, calculo,
 *     persistencia, notificacion, eventos, reportes y presentacion.
 *
 *  SOLUCION
 *     OrderService actua como FACADE.
 *     Coordina servicios especializados mediante dependencias inyectadas
 *     por constructor.
 *
 *     Cada servicio tiene una responsabilidad concreta y la fachada
 *     solamente coordina el flujo.
 *
 *  CONSECUENCIA NEGATIVA
 *     La complejidad no desaparece: se distribuye entre varias clases.
 *     Esto aumenta la cantidad de archivos y dependencias, pero permite
 *     probar y modificar cada responsabilidad de forma independiente.
 * ============================================================================
 */


/**
 * ============================================================================
 *  SERVICIOS COHESIVOS
 * ============================================================================
 */

final class OrderValidator
{
    public function validate(string $paciente, float $monto): void
    {
        if (trim($paciente) === '') {
            throw new InvalidArgumentException('Falta el paciente');
        }

        if ($monto <= 0) {
            throw new InvalidArgumentException('Monto invalido');
        }
    }
}


final class OrderPricingService
{
    public function calculate(float $monto, string $tipoPaciente): float
    {
        if ($tipoPaciente === 'obra_social') {
            return $monto * 0.7;
        }

        if ($tipoPaciente === 'jubilado') {
            return $monto * 0.5;
        }

        return $monto;
    }
}


final class OrderRepository
{
    public function save(Order $order): void
    {
        $order->guardar();
    }
}


final class OrderNotificationService
{
    public function notify(
        int $id,
        float $total,
        string $tipoNotificacion,
        string $destino
    ): void {
        if ($tipoNotificacion === 'email') {
            $email = new EmailNotification();
            $email->enviarEmail(
                $destino,
                'Pedido creado',
                "Pedido {$id} por $ {$total}"
            );
        }

        if ($tipoNotificacion === 'sms') {
            $sms = new SmsNotification();
            $sms->mandarSms(
                $destino,
                "Pedido {$id} por $ {$total}"
            );
        }
    }
}


/**
 * ============================================================================
 *  FACADE
 * ============================================================================
 *
 * OrderService coordina los servicios.
 * No implementa directamente las reglas de cada responsabilidad.
 */

final class OrderService
{
    public function __construct(
        private OrderValidator $validator,
        private OrderPricingService $pricing,
        private OrderRepository $repository,
        private OrderNotificationService $notifications,
        private OrderEvents $events,
        private ReportGenerator $reports
    ) {
    }

    public function procesarPedidoCompleto(
        int $id,
        string $paciente,
        float $monto,
        string $tipoPaciente,
        string $tipoNotificacion,
        string $destino
    ): Order {
        $this->validator->validate($paciente, $monto);

        $total = $this->pricing->calculate($monto, $tipoPaciente);

        $order = new Order(
            $id,
            $paciente,
            $total,
            $tipoPaciente
        );

        $this->repository->save($order);

        $this->notifications->notify(
            $id,
            $total,
            $tipoNotificacion,
            $destino
        );

        $this->events->pedidoCreado($order);

        return $order;
    }
}