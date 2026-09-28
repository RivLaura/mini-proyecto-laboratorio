<?php

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Order;
use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM orders");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Order(
            $row['id'],
            (float)$row['amount'],
            $row['customer_email'],
            $row['status']
        ), $rows);
    }
}