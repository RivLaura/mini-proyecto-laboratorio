<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class StrategyTest extends TestCase
{
    public function testPacienteParticular(): void
    {
        $calculadora = new PriceCalculator(new PrivatePatientStrategy());

        $this->assertEqualsWithDelta(10000, $calculadora->calculate(10000), 0.001);
        $this->assertEqualsWithDelta(12100, $calculadora->calculateWithTax(10000), 0.001);
    }

    public function testObraSocialConDescuentoDel30PorCiento(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(7000, $calculadora->calculate(10000), 0.001);
        $this->assertEqualsWithDelta(8470, $calculadora->calculateWithTax(10000), 0.001);
    }

    public function testJubiladoConservaLaPoliticaOriginalDeIva(): void
    {
        $calculadora = new PriceCalculator(
            new RetiredPatientStrategy(),
            new PrivatePatientStrategy()
        );

        $this->assertEqualsWithDelta(5000, $calculadora->calculate(10000), 0.001);
        $this->assertEqualsWithDelta(12100, $calculadora->calculateWithTax(10000), 0.001);
    }

    public function testJubiladoSinEstrategiaAlternativaDeIva(): void
    {
        $calculadora = new PriceCalculator(new RetiredPatientStrategy());

        $this->assertEqualsWithDelta(6050, $calculadora->calculateWithTax(10000), 0.001);
    }

    public function testPrepagaConDescuentoDel20PorCiento(): void
    {
        $calculadora = new PriceCalculator(new PrepaidStrategy(0.20));

        $this->assertEqualsWithDelta(8000, $calculadora->calculate(10000), 0.001);
        $this->assertEqualsWithDelta(9680, $calculadora->calculateWithTax(10000), 0.001);
    }

    public function testObraSocialConDescuentoPersonalizado(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy(0.40));

        $this->assertEqualsWithDelta(6000, $calculadora->calculate(10000), 0.001);
    }

    public static function descuentosInvalidos(): array
    {
        return [
            'descuento negativo' => [-0.01],
            'descuento mayor al 100 por ciento' => [1.01],
            'descuento infinito' => [INF],
            'descuento NaN' => [NAN],
        ];
    }

    #[DataProvider('descuentosInvalidos')]
    public function testObraSocialRechazaDescuentosInvalidos(float $descuento): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InsuranceStrategy($descuento);
    }

    #[DataProvider('descuentosInvalidos')]
    public function testPrepagaRechazaDescuentosInvalidos(float $descuento): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PrepaidStrategy($descuento);
    }

    public function testPermiteAgregarNuevaEstrategia(): void
    {
        $estrategia = new class implements PricingStrategy {
            public function calculate(float $amount): float
            {
                return $amount + 1000.0;
            }
        };

        $calculadora = new PriceCalculator($estrategia);

        $this->assertEqualsWithDelta(11000, $calculadora->calculate(10000), 0.001);
    }
}
