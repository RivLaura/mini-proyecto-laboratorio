<?php
/**
 * ============================================================================
 *  CALCULO DE PRECIOS
 *  Patron esperado: STRATEGY (comportamiento)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     Un switch concentra TODOS los algoritmos de precio.
 *     Cada tipo de paciente nuevo obliga a modificar esta clase.
 *     Los porcentajes estan escritos a mano y repetidos en otros archivos.
 *
 *  ✅ FORMA CORRECTA
 *     Interfaz PricingStrategy + una clase por algoritmo + composicion.
 *     Agregar "prepaga" pasa a ser AGREGAR una clase, no MODIFICAR esta.
 * ============================================================================
 */

final class PriceCalculator
{
    public function __construct(
        private PricingStrategy $strategy
    ) {
    }

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }

    public function calculateWithTax(float $amount): float
    {
        return $this->calculate($amount) * 1.21;
    }
}