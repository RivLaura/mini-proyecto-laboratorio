<?php
/**
 * ============================================================================
 *  VISTA: listado de pedidos
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     1. La vista consulta la base de datos. Es el error mas comun al "aplicar MVC".
 *     2. La vista calcula totales (regla de negocio en la capa de presentacion).
 *     3. Los datos se imprimen sin escapar => XSS.
 *
 *  ✅ FORMA CORRECTA
 *     La vista recibe datos ya listos y SOLO los muestra, escapando la salida.
 *     Si una vista necesita consultar algo, el problema esta en el controlador.
 * ============================================================================
 */
?>
<h1>Pedidos</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Paciente</th>
        <th>Total</th>
    </tr>

    <?php foreach ($pedidos as $pedido): ?>
        <tr>
            <td><?= htmlspecialchars(
                (string) $pedido['id'],
                ENT_QUOTES,
                'UTF-8'
            ) ?></td>

            <td><?= htmlspecialchars(
                $pedido['paciente'],
                ENT_QUOTES,
                'UTF-8'
            ) ?></td>

            <td><?= htmlspecialchars(
                (string) $pedido['total'],
                ENT_QUOTES,
                'UTF-8'
            ) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
/*
 * ----------------------------------------------------------------------------
 * EJERCICIO 8 (TP): dejar esta vista sin una sola linea de logica:
 * sin conexion, sin calculo y con toda la salida escapada.
 *
 * PREGUNTA PARA EL PR: si el descuento de obra social aparece en Order.php,
 * en PriceCalculator.php, en OrderService.php, en OrderController.php y aca,
 * cuantos lugares hay que tocar cuando el laboratorio lo cambie a 25%?
 * Ese numero es la medida exacta de la deuda tecnica del proyecto.
 * ----------------------------------------------------------------------------
 */
