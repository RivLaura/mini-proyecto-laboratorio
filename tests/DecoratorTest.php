<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DecoratorTest extends TestCase
{
    public function testReporteBasicoGeneraElContenidoBase(): void
    {
        $reporte = new BasicReport('Pedido 901');

        $this->assertInstanceOf(Report::class, $reporte);
        $this->assertSame('Reporte: Pedido 901', $reporte->generate());
    }

    public function testFirmaDigitalAgregaSuTexto(): void
    {
        $reporte = new DigitalSignatureDecorator(new BasicReport('Pedido 901'));

        $this->assertSame('Reporte: Pedido 901 + firma digital', $reporte->generate());
    }

    public function testPdfAgregaSuTexto(): void
    {
        $reporte = new PdfReportDecorator(new BasicReport('Pedido 901'));

        $this->assertSame('Reporte: Pedido 901 + PDF', $reporte->generate());
    }

    public function testMarcaDeAguaAgregaSuTexto(): void
    {
        $reporte = new WatermarkDecorator(new BasicReport('Pedido 901'));

        $this->assertSame('Reporte: Pedido 901 + marca de agua', $reporte->generate());
    }

    public function testLosDecoradoresSonIntercambiablesConUnReport(): void
    {
        $decoradores = [
            new DigitalSignatureDecorator(new BasicReport('X')),
            new PdfReportDecorator(new BasicReport('X')),
            new WatermarkDecorator(new BasicReport('X')),
        ];

        foreach ($decoradores as $decorador) {
            $this->assertInstanceOf(Report::class, $decorador);
            $this->assertInstanceOf(ReportDecorator::class, $decorador);
        }
    }

    /**
     * Las ocho combinaciones que antes se pedian con
     * ReportGenerator::generate($contenido, $firma, $pdf, $marcaDeAgua).
     * Los resultados esperados son los que producia esa clase.
     */
    public static function combinaciones(): array
    {
        return [
            'sin agregados' => [false, false, false, 'Reporte: Pedidos del dia'],
            'solo firma' => [true, false, false, 'Reporte: Pedidos del dia + firma digital'],
            'solo PDF' => [false, true, false, 'Reporte: Pedidos del dia + PDF'],
            'solo marca de agua' => [false, false, true, 'Reporte: Pedidos del dia + marca de agua'],
            'firma y PDF' => [true, true, false, 'Reporte: Pedidos del dia + firma digital + PDF'],
            'firma y marca de agua' => [true, false, true, 'Reporte: Pedidos del dia + firma digital + marca de agua'],
            'PDF y marca de agua' => [false, true, true, 'Reporte: Pedidos del dia + PDF + marca de agua'],
            'completo' => [true, true, true, 'Reporte: Pedidos del dia + firma digital + PDF + marca de agua'],
        ];
    }

    #[DataProvider('combinaciones')]
    public function testLasOchoCombinacionesProducenElResultadoEsperado(
        bool $conFirma,
        bool $conPdf,
        bool $conMarcaDeAgua,
        string $esperado
    ): void {
        $reporte = new BasicReport('Pedidos del dia');

        if ($conFirma) {
            $reporte = new DigitalSignatureDecorator($reporte);
        }

        if ($conPdf) {
            $reporte = new PdfReportDecorator($reporte);
        }

        if ($conMarcaDeAgua) {
            $reporte = new WatermarkDecorator($reporte);
        }

        $this->assertSame($esperado, $reporte->generate());
    }

    public function testElOrdenDeLosDecoradoresModificaElResultado(): void
    {
        $firmaLuegoPdf = new PdfReportDecorator(
            new DigitalSignatureDecorator(new BasicReport('Pedido 7'))
        );
        $pdfLuegoFirma = new DigitalSignatureDecorator(
            new PdfReportDecorator(new BasicReport('Pedido 7'))
        );

        $this->assertSame('Reporte: Pedido 7 + firma digital + PDF', $firmaLuegoPdf->generate());
        $this->assertSame('Reporte: Pedido 7 + PDF + firma digital', $pdfLuegoFirma->generate());
        $this->assertNotSame($firmaLuegoPdf->generate(), $pdfLuegoFirma->generate());
    }

    public function testSePuedeAgregarUnDecoradorNuevoSinModificarLasClasesExistentes(): void
    {
        $base = new PdfReportDecorator(new BasicReport('Pedido 12'));

        $conCodigoQr = new class ($base) extends ReportDecorator {
            public function generate(): string
            {
                return $this->report->generate() . ' + codigo QR';
            }
        };

        $this->assertSame('Reporte: Pedido 12 + PDF + codigo QR', $conCodigoQr->generate());
    }

    public function testComposicionUsadaPorOrderServiceConservaElReporteOriginal(): void
    {
        // Equivale al antiguo generate("Pedido 901", true, true, false).
        $reporte = new PdfReportDecorator(
            new DigitalSignatureDecorator(
                new BasicReport('Pedido 901')
            )
        );

        $this->assertSame('Reporte: Pedido 901 + firma digital + PDF', $reporte->generate());
    }

    public function testLaRutaReporteUsaLosDecoradores(): void
    {
        $this->expectOutputString(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua'
        );

        (new OrderController())->report();
    }

    public function testReportGeneratorFueEliminado(): void
    {
        $this->assertFalse(class_exists('ReportGenerator'));
    }
}
