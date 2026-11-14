<?php
/**
 * ============================================================================
 *  VISTA: listado de pedidos
 * ============================================================================
 *
 *  Vista MVC: recibe $pedidos ya preparado por el controlador y solo se
 *  limita a mostrar los datos, escapando la salida.
 *
 *  No contiene consultas a la base de datos, ni calculos de descuentos,
 *  ni logica de negocio.
 */

declare(strict_types=1);
?>
<h1>Pedidos</h1>

<table border="1">
    <tr><th>ID</th><th>Paciente</th><th>Total</th></tr>

    <?php foreach ($pedidos as $pedido): ?>
        <tr>
            <td><?= htmlspecialchars((string) $pedido['id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $pedido['paciente'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $pedido['total'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
