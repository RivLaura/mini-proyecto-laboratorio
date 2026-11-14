<?php

final class PrepaidStrategy implements PricingStrategy
{
    public function __construct(private float $discount)
    {
        if (!is_finite($discount) || $discount < 0 || $discount > 1) {
            throw new InvalidArgumentException(
                'El descuento debe estar entre 0 y 1.'
            );
        }
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}