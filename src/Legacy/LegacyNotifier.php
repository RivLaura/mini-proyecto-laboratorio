<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 *  Patron aplicado: ADAPTER (estructural)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, nuestro sistema modificaba directamente una clase de terceros
 *     y tambien copiaba su comportamiento en otra clase.
 *
 *  SOLUCION
 *     LegacyNotifierAdapter actua como ADAPTER.
 *     Implementa nuestro contrato Notification y traduce la llamada
 *     send() de nuestro sistema a sendMessage() del sistema heredado.
 *
 *     Nuestro sistema conoce Notification.
 *     El Adapter conoce tanto Notification como LegacyNotifier.
 *
 *  CONSECUENCIA NEGATIVA
 *     Se agrega una clase intermedia y una pequeña capa de indirección.
 *
 *     Esto aumenta ligeramente la cantidad de clases, pero evita modificar
 *     el sistema heredado y concentra la incompatibilidad en un unico lugar.
 * ============================================================================ */


/**
 * ============================================================================
 *  CONTRATO DE NUESTRO SISTEMA
 * ============================================================================
 *
 * Nuestro sistema trabaja con este contrato.
 * No necesita conocer la interfaz real del sistema heredado.
 */

interface Notification
{
    public function send(string $message): void;
}


/**
 * ============================================================================
 *  SISTEMA LEGACY
 * ============================================================================
 *
 * Esta clase pertenece al proveedor externo.
 * No debe modificarse.
 */

class LegacyNotifier
{
    /** Metodo original del proveedor. No lo podemos cambiar. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}


/**
 * ============================================================================
 *  ADAPTER
 * ============================================================================
 *
 * LegacyNotifierAdapter adapta la interfaz del sistema heredado
 * al contrato que necesita nuestro sistema.
 *
 * COMPOSICION:
 * el Adapter tiene una instancia de LegacyNotifier.
 */

final class LegacyNotifierAdapter implements Notification
{
    private LegacyNotifier $legacy;

    public function __construct(LegacyNotifier $legacy)
    {
        $this->legacy = $legacy;
    }

    /**
     * Traduce nuestro metodo send() al metodo sendMessage()
     * que utiliza el sistema heredado.
     */
    public function send(string $message): void
    {
        $this->legacy->sendMessage($message);
    }
}