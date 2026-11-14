<?php
/**
 * EJERCICIO 4 — Agrega la marca de agua al reporte que envuelve.
 */
final class WatermarkDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}
