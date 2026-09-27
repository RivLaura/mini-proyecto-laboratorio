<?php
/**
 * ============================================================================
 *  GENERACION DE REPORTES
 *  Patron aplicado: DECORATOR (estructural)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, ReportGenerator recibia varias banderas booleanas para decidir
 *     que agregados aplicar.
 *
 *  SOLUCION
 *     Se define el contrato Report.
 *     BasicReport representa el reporte base.
 *     Los agregados se implementan como decoradores que envuelven otro Report.
 *
 *     Cada decorador agrega una responsabilidad sin modificar el reporte
 *     original ni agregar parametros booleanos.
 *
 *  CONSECUENCIA NEGATIVA
 *     El orden de los decoradores puede modificar el resultado final.
 *
 *     Por ejemplo, firmar antes de convertir a PDF no necesariamente produce
 *     el mismo resultado que convertir a PDF antes de firmar.
 *
 *     Por eso, el orden de los decoradores debe documentarse cuando exista
 *     una dependencia entre ellos.
 * ============================================================================
 */


/**
 * ============================================================================
 *  CONTRATO REPORT
 * ============================================================================
 *
 * Todos los reportes y decoradores deben cumplir la misma interfaz.
 */

interface Report
{
    public function generate(): string;
}


/**
 * ============================================================================
 *  REPORTE BASE
 * ============================================================================
 *
 * Contiene solamente la informacion original del reporte.
 */

final class BasicReport implements Report
{
    private string $contenido;

    public function __construct(string $contenido)
    {
        $this->contenido = $contenido;
    }

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}


/**
 * ============================================================================
 *  DECORADOR DE FIRMA DIGITAL
 * ============================================================================
 *
 * Envuelve otro Report y agrega la firma digital.
 */

final class DigitalSignatureDecorator implements Report
{
    private Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}


/**
 * ============================================================================
 *  EJERCICIO 4 — DECORADOR PDF
 * ============================================================================
 *
 * Agrega la generacion en formato PDF sin modificar el reporte original.
 */

final class PdfReportDecorator implements Report
{
    private Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}


/**
 * ============================================================================
 *  EJERCICIO 4 — DECORADOR MARCA DE AGUA
 * ============================================================================
 *
 * Agrega la marca de agua sin modificar el reporte original.
 */

final class WatermarkDecorator implements Report
{
    private Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}