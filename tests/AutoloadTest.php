<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AutoloadTest extends TestCase
{
    public function testCargaClasesDeDistintasCarpetas(): void
    {
        $clases = [
            'Connection',
            'Order',
            'OrderController',
            'OrderService',
            'PriceCalculator',
            'NotificationSender',
            'LegacyNotifier',
            'OrderEvents',
        ];

        foreach ($clases as $clase) {
            $this->assertTrue(
                class_exists($clase),
                "No se pudo cargar la clase: {$clase}"
            );
        }
    }

    public function testClaseInexistenteNoProduceError(): void
    {
        $this->assertFalse(
            class_exists('ClaseInexistenteDePrueba')
        );
    }
}
