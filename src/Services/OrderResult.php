<?php
/**
 * Resultado de procesar un pedido.
 *
 * La fachada devuelve datos en lugar de imprimir HTML: el controlador o
 * la vista deciden como mostrarlos (MVC).
 */
final class OrderResult
{
    public function __construct(
        public readonly Order $order,
        public readonly float $total,
        public readonly string $reporte
    ) {
    }
}
