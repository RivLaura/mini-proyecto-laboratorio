<?php
/**
 * Avisa al paciente que su pedido fue creado.
 *
 * La creacion del canal concreto (email, sms, whatsapp) la resuelve
 * NotificationFactory (Factory, #15): aca no hay if por tipo ni new de
 * notificadores concretos. Se conservan el asunto y el texto del mensaje
 * que usaba procesarPedidoCompleto().
 */
class OrderNotificationService
{
    /** @throws InvalidArgumentException si el tipo no esta soportado. */
    public function notify(Order $order, string $tipoNotificacion, string $destino): void
    {
        NotificationFactory::create($tipoNotificacion, $destino, 'Pedido creado')
            ->send("Pedido {$order->id} por $ {$order->amount}");
    }
}
