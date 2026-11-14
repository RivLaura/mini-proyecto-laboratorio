<?php
/**
 * ============================================================================
 *  VISTA: resultado de creacion de un pedido
 * ============================================================================
 *
 *  Vista MVC: recibe $resultado (OrderResult) ya preparado por el controlador
 *  y solo se limita a presentar los datos, escapando la salida.
 *
 *  No contiene consultas a la base de datos, ni calculos de descuentos,
 *  ni logica de negocio.
 */

declare(strict_types=1);
?>
<h1>Pedido creado</h1>
<p>Paciente: <?= htmlspecialchars((string) $resultado->order->patient, ENT_QUOTES, 'UTF-8') ?></p>
<p>Total: $ <?= htmlspecialchars((string) $resultado->total, ENT_QUOTES, 'UTF-8') ?></p>
