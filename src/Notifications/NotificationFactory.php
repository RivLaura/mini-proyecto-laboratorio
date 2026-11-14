<?php

final class NotificationFactory
{
    public static function create(
        string $tipo,
        string $destino,
        string $asunto = 'Pedido del laboratorio'
    ): Notification {
        return match ($tipo) {
            'email' => new EmailNotification($destino, $asunto),
            'sms' => new SmsNotification($destino),
            'whatsapp' => new WhatsAppNotification($destino),
            default => throw new InvalidArgumentException(
                "Tipo de notificacion no soportado: {$tipo}"
            ),
        };
    }
}