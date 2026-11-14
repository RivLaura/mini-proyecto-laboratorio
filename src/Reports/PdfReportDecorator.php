<?php
/**
 * EJERCICIO 4 — Agrega el formato PDF al reporte que envuelve.
 * Es una descripcion de texto: no genera un archivo PDF real.
 */
final class PdfReportDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}
