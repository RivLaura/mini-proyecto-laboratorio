<?php
/**
 * Arma el reporte de un pedido componiendo decoradores (Decorator).
 *
 * Reemplaza a generate("Pedido {$id}", true, true, false):
 * firma digital + PDF, en el orden acordado para el equipo.
 */
class OrderReportService
{
    public function paraPedido(Order $order): string
    {
        $reporte = new PdfReportDecorator(
            new DigitalSignatureDecorator(
                new BasicReport("Pedido {$order->id}")
            )
        );

        return $reporte->generate();
    }
}
