<?php
/**
 * Selecciona la PricingStrategy que corresponde al tipo de paciente y
 * delega el calculo en PriceCalculator (Strategy, #14).
 *
 * No contiene porcentajes ni reglas de descuento: el mapa
 * "tipo de paciente => estrategia" se recibe ya armado
 * (ver OrderServiceFactory). Agregar un tipo de paciente es agregar una
 * entrada al mapa, no modificar esta clase.
 */
class OrderPricingService
{
    /**
     * @param array<string, PricingStrategy> $strategies
     * @param PricingStrategy $default para tipos no registrados (el codigo
     *        original cobraba el monto completo en ese caso).
     */
    public function __construct(
        private array $strategies,
        private PricingStrategy $default
    ) {
    }

    public function calculate(float $monto, string $tipoPaciente): float
    {
        $strategy = $this->strategies[$tipoPaciente] ?? $this->default;

        return (new PriceCalculator($strategy))->calculate($monto);
    }
}
