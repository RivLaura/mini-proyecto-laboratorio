<?php
if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Órdenes</title>
</head>
<body>
    <h1>Órdenes Registradas</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e((string)$order->getId()) ?></td>
                        <td><?= e($order->getCustomerEmail()) ?></td>
                        <td>$<?= number_format($order->getAmount(), 2) ?></td>
                        <td><?= e($order->getStatus()) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron órdenes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>