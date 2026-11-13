<?php
/**
 * Contrato de los observadores del evento "pedido creado".
 *
 * OrderEvents solo conoce esta interfaz: cualquier clase que la implemente
 * puede suscribirse sin modificar el sujeto.
 */
interface OrderObserver
{
    public function update(Order $order): void;
}
