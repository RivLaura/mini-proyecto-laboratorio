<?php
/**
 * ============================================================================
 *  ADAPTER DEL SISTEMA HEREDADO
 *  Patron aplicado: ADAPTER (estructural)
 * ============================================================================
 *
 *  Nuestro sistema trabaja con el contrato Notification
 *  (src/Notifications/Notification.php, definido por Factory):
 *
 *      send(string $message): void
 *
 *  El proveedor externo expone otra interfaz:
 *
 *      LegacyNotifier::sendMessage(string $text): void
 *
 *  LegacyNotifierAdapter implementa NUESTRO contrato y TIENE adentro al
 *  sistema heredado (composicion). Es el unico lugar del sistema que
 *  conoce sendMessage().
 *
 *  Estructura:
 *      Nuestro sistema -> Notification <- LegacyNotifierAdapter -> LegacyNotifier
 *
 *  Uso:
 *      $notificacion = new LegacyNotifierAdapter(new LegacyNotifier());
 *      $notificacion->send('Pedido creado');
 *
 *  PREGUNTA DEL EJERCICIO 3: si manana el proveedor cambia sendMessage()
 *  por dispatch(), cambia UN solo archivo: este.
 *
 *  CONSECUENCIA NEGATIVA
 *     Se agrega una clase intermedia y una capa de indireccion. Ademas,
 *     el adapter solo puede exponer lo que el sistema heredado ofrece:
 *     si Notification creciera (por ejemplo, con un asunto), el adapter
 *     tendria que resolver como traducirlo o no podria soportarlo.
 * ============================================================================
 */

final class LegacyNotifierAdapter implements Notification
{
    public function __construct(private LegacyNotifier $legacy)
    {
    }

    /** Traduce send() de nuestro sistema a sendMessage() del proveedor. */
    public function send(string $message): void
    {
        $this->legacy->sendMessage($message);
    }
}
