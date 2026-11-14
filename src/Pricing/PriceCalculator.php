<?php
/**
 * La estrategia principal calcula el precio sin IVA.
 *
 * Si no se indica taxStrategy, se utiliza la estrategia principal
 * tambien para calcular el precio con IVA.
 *
 * Para conservar el comportamiento original de jubilados:
 * new PriceCalculator(
 *     new RetiredPatientStrategy(),
 *     new PrivatePatientStrategy()
 * );
 *
 * Esta configuracion aplica el descuento sin IVA y utiliza
 * el importe completo como base del calculo con IVA.
 */

final class PriceCalculator
{
    public function __construct(
        private PricingStrategy $strategy,
        private ?PricingStrategy $taxStrategy = null
    ) {
    }

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }

    public function calculateWithTax(float $amount): float
    {
        $strategy = $this->taxStrategy ?? $this->strategy;

        return $strategy->calculate($amount) * 1.21;
    }
}