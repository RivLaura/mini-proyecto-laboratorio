<?php
/**
 * ============================================================================
 *  NOTIFICACION POR EMAIL
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Esta clase NO implementa ninguna interfaz.
 *     Quien la usa depende de la clase concreta => acoplamiento fuerte.
 *     El metodo se llama enviarEmail(); el de SMS se llama mandarSms().
 *     Nombres distintos para la misma responsabilidad: imposible intercambiarlos.
 *
 *  ✅ FORMA CORRECTA
 *     interface Notification { public function send(string $message): void; }
 *     class EmailNotification implements Notification { public function send(...) }
 *     Mismo contrato => polimorfismo => el cliente no conoce la clase concreta.
 * ============================================================================
 */

final class EmailNotification implements Notification
{
    public function __construct(
        private string $destinatario,
        private string $asunto = 'Pedido del laboratorio'
    ) {
    }

    public function send(string $message): void
    {
        echo "[EMAIL] para {$this->destinatario} | {$this->asunto}: {$message}<br>";
    }
}
