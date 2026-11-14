<?php
/**
 * Reporte base: contiene solamente la informacion original del reporte.
 * Es el componente que envuelven los decoradores.
 */
final class BasicReport implements Report
{
    public function __construct(private string $contenido)
    {
    }

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}
