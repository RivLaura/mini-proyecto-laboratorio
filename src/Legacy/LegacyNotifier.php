<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 *  Patron aplicado: ADAPTER (estructural) — ver LegacyNotifierAdapter.php
 * ============================================================================
 *
 *  CONTEXTO: el laboratorio ya tenia un sistema de avisos hecho por otro
 *  proveedor. No lo podemos modificar: lo usan tambien Recepcion y Facturacion.
 *
 *  DEUDA CORREGIDA
 *     1. Se elimino el metodo send() que habiamos agregado DENTRO de esta
 *        clase de terceros. La clase vuelve a ser la original del proveedor.
 *     2. Se elimino CopiaDeLegacyEnNuestroSistema, que duplicaba el cuerpo
 *        de sendMessage() "para no depender" del sistema heredado.
 *
 *  SOLUCION
 *     La incompatibilidad queda encerrada en LegacyNotifierAdapter, que
 *     implementa nuestro contrato Notification (definido por Factory)
 *     y traduce send() -> sendMessage().
 * ============================================================================
 */

class LegacyNotifier
{
    /** Metodo original del proveedor. No lo podemos cambiar. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}
