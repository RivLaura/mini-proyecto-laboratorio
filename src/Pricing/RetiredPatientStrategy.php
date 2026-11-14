<?php

final class RetiredPatientStrategy implements PricingStrategy
{
    public function calculate(float $amount): float
    {
        return $amount * 0.5;
    }
}