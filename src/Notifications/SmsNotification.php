<?php
/**
 * ============================================================================
 *  NOTIFICACION POR SMS
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Misma responsabilidad que EmailNotification pero con OTRA firma:
 *        EmailNotification::enviarEmail($destinatario, $asunto, $cuerpo)
 *        SmsNotification::mandarSms($numero, $texto)
 *     Al no compartir contrato, el cliente necesita un if por cada tipo.
 *     Ese if es exactamente la deuda que despues repara Factory.
 *
 *  ✅ FORMA CORRECTA
 *     Ambas implementan Notification::send(string $message): void.
 * ============================================================================
 */

final class SmsNotification implements Notification
{
    public function __construct(private string $numero)
    {
    }

    public function send(string $message): void
    {
        echo "[SMS] a {$this->numero}: {$message}<br>";
    }
}
