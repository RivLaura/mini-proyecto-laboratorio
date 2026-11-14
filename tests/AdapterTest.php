<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AdapterTest extends TestCase
{
    public function testAdapterImplementaLaInterfazNotification(): void
    {
        $adapter = new LegacyNotifierAdapter(new LegacyNotifier());

        $this->assertInstanceOf(Notification::class, $adapter);
    }

    public function testSendDelegaEnSendMessageDelSistemaHeredado(): void
    {
        $legacy = $this->createMock(LegacyNotifier::class);
        $legacy->expects($this->once())
            ->method('sendMessage')
            ->with('Pedido creado');

        $adapter = new LegacyNotifierAdapter($legacy);
        $adapter->send('Pedido creado');
    }

    public function testCadaEnvioInvocaUnaSolaVezAlMetodoHeredado(): void
    {
        $legacy = $this->createMock(LegacyNotifier::class);
        $legacy->expects($this->exactly(2))
            ->method('sendMessage');

        $adapter = new LegacyNotifierAdapter($legacy);
        $adapter->send('Pedido 1');
        $adapter->send('Pedido 2');
    }

    public static function mensajes(): array
    {
        return [
            'texto simple' => ['Pedido creado'],
            'con importe' => ['Pedido 901 por $ 7000'],
            'con acentos' => ['Atención: análisis listo'],
            'texto vacio' => [''],
        ];
    }

    #[DataProvider('mensajes')]
    public function testElMensajeSeTransmiteSinAlteraciones(string $mensaje): void
    {
        $legacy = $this->createMock(LegacyNotifier::class);
        $legacy->expects($this->once())
            ->method('sendMessage')
            ->with($this->identicalTo($mensaje));

        (new LegacyNotifierAdapter($legacy))->send($mensaje);
    }

    public function testSeConservaLaSalidaDelSistemaHeredado(): void
    {
        $adapter = new LegacyNotifierAdapter(new LegacyNotifier());

        $this->expectOutputString('[LEGACY] Pedido 901 creado<br>');

        $adapter->send('Pedido 901 creado');
    }

    public function testElAdapterProduceLaMismaSalidaQueElSistemaHeredadoDirecto(): void
    {
        ob_start();
        (new LegacyNotifier())->sendMessage('Resultado disponible');
        $salidaDirecta = ob_get_clean();

        ob_start();
        (new LegacyNotifierAdapter(new LegacyNotifier()))->send('Resultado disponible');
        $salidaAdaptada = ob_get_clean();

        $this->assertSame($salidaDirecta, $salidaAdaptada);
    }

    public function testLaClaseHeredadaNoSeModificaParaImplementarNotification(): void
    {
        $heredada = new ReflectionClass(LegacyNotifier::class);

        $this->assertFalse($heredada->implementsInterface(Notification::class));
        $this->assertFalse($heredada->hasMethod('send'));
        $this->assertTrue($heredada->hasMethod('sendMessage'));
    }

    public function testElAdapterSeUsaDondeElSistemaEsperaUnaNotification(): void
    {
        $enviar = static function (Notification $notificacion, string $mensaje): void {
            $notificacion->send($mensaje);
        };

        $this->expectOutputString('[LEGACY] Pedido 15 listo<br>');

        $enviar(new LegacyNotifierAdapter(new LegacyNotifier()), 'Pedido 15 listo');
    }
}
