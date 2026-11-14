<?php
/**
 * Base comun de los decoradores de reportes.
 *
 * Un decorador ES un Report (cumple el mismo contrato) y TIENE un Report
 * adentro (composicion). Centraliza el constructor para no repetirlo en
 * cada decorador concreto; cada subclase solo define que agrega.
 */
abstract class ReportDecorator implements Report
{
    public function __construct(protected Report $report)
    {
    }
}
