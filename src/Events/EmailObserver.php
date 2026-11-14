<?php
/**
 * Observador que avisa por email que se creo un pedido.
 */
final class EmailObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "Email enviado para el pedido {$order->id}<br>";
    }
}
