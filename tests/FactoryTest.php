<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

final class FactoryTest extends TestCase
{
    public static function canalesValidos(): array
    {
        return [
            'email' => [
                'email',
                EmailNotification::class,
                '[EMAIL] para destino-prueba | Pedido del laboratorio: Mensaje de prueba<br>',
            ],
            'sms' => [
                'sms',
                SmsNotification::class,
                '[SMS] a destino-prueba: Mensaje de prueba<br>',
            ],
            'whatsapp' => [
                'whatsapp',
                WhatsAppNotification::class,
                '[WHATSAPP] a destino-prueba: Mensaje de prueba<br>',
            ],
        ];
    }

    #[DataProvider('canalesValidos')]
    public function testFactoryCreaNotificacionCorrecta(
        string $tipo,
        string $claseEsperada,
        string $salidaEsperada
    ): void {
        $notificacion = NotificationFactory::create(
            $tipo,
            'destino-prueba'
        );

        $this->assertInstanceOf(Notification::class, $notificacion);
        $this->assertInstanceOf($claseEsperada, $notificacion);
    }

    #[DataProvider('canalesValidos')]
    public function testNotificationSenderEnviaMensaje(
        string $tipo,
        string $claseEsperada,
        string $salidaEsperada
    ): void {
        $this->expectOutputString($salidaEsperada);

        $sender = new NotificationSender();

        $sender->enviar(
            $tipo,
            'destino-prueba',
            'Mensaje de prueba'
        );
    }

    public function testEmailPermiteAsuntoPersonalizado(): void
    {
        $this->expectOutputString(
            '[EMAIL] para correo@prueba.com | Pedido creado: Mensaje personalizado<br>'
        );

        $notificacion = NotificationFactory::create(
            'email',
            'correo@prueba.com',
            'Pedido creado'
        );

        $notificacion->send('Mensaje personalizado');
    }

    public function testFactoryRechazaTipoDesconocido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        NotificationFactory::create(
            'fax',
            'destino-prueba'
        );
    }

    public function testNotificationSenderRechazaTipoDesconocido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $sender = new NotificationSender();

        $sender->enviar(
            'fax',
            'destino-prueba',
            'Mensaje de prueba'
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testOrderServiceProcesaPedidoConWhatsApp(): void
    {
        $conexion = Connection::obtener();
        $cantidadInicial = count($conexion->consultas());

        $this->expectOutputRegex(
            '/\[WHATSAPP\] a destino-prueba: Pedido 901 por \$ 1000<br>/'
        );

        $service = OrderServiceFactory::create();

        $resultado = $service->procesarPedidoCompleto(
            901,
            'Paciente Factory',
            1000.0,
            'particular',
            'whatsapp',
            'destino-prueba'
        );

        $this->assertInstanceOf(OrderResult::class, $resultado);
        $this->assertEqualsWithDelta(1000.0, $resultado->total, 0.001);

        $consultas = $conexion->consultas();

        $this->assertCount(
            $cantidadInicial + 1,
            $consultas
        );

        $this->assertStringContainsString(
            "VALUES (901, 'Paciente Factory', 1000)",
            $consultas[array_key_last($consultas)]
        );
    }
}
