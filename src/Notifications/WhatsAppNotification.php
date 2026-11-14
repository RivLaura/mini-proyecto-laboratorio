<?php

final class WhatsAppNotification implements Notification
{
    public function __construct(private string $numero)
    {
    }

    public function send(string $message): void
    {
        echo "[WHATSAPP] a {$this->numero}: {$message}<br>";
    }
}