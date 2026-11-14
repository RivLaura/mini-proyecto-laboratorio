<?php

class OrderRepository
{
    public function save(Order $order): void
    {
        $conexion = Connection::obtener();

        $conexion->ejecutar(
            "INSERT INTO orders (id, patient, amount) VALUES ({$order->id}, '{$order->patient}', {$order->amount})"
        );
    }
}