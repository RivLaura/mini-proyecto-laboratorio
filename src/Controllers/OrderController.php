<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;

class OrderController
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    public function index(): void
    {
        $orders = $this->orderRepository->findAll();
        require __DIR__ . '/../../views/orders.php';
    }
}