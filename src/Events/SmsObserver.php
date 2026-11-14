<?php
/**
 * EJERCICIO 5 — SmsObserver
 *
 * Se agrega un nuevo observador implementando OrderObserver.
 * OrderEvents NO se modifico para incorporarlo.
 */
final class SmsObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "SMS enviado para el pedido {$order->id}<br>";
    }
}
