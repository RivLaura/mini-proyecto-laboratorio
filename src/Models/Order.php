<?php

namespace App\Models;

class Order
{
    private ?int $id;
    private float $amount;
    private string $customerEmail;
    private string $status;

    public function __construct(?int $id, float $amount, string $customerEmail, string $status = 'pending')
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->customerEmail = $customerEmail;
        $this->status = $status;
    }

    public function getId(): ?int { return $this->id; }
    public function getAmount(): float { return $this->amount; }
    public function getCustomerEmail(): string { return $this->customerEmail; }
    public function getStatus(): string { return $this->status; }
}