<?php

final class WhatsAppNotification implements Notification
{
    public function __construct(private string $numero)
    {
    }

    public function send(string $message): void
    {
        echo "[WHATSAPP] a " . htmlspecialchars((string) $this->numero, ENT_QUOTES, 'UTF-8') . ": {$message}<br>";
    }
}