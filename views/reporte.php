<?php
/**
 * ============================================================================
 *  VISTA: reporte de pedidos
 * ============================================================================
 *
 *  Vista MVC: recibe $reporte ya generado por el controlador
 *  (composicion de decoradores) y solo se limita a mostrarlo,
 *  escapando la salida.
 *
 *  No contiene logica de negocio ni composicion de decoradores.
 */

declare(strict_types=1);
?>
<?= htmlspecialchars((string) $reporte, ENT_QUOTES, 'UTF-8') ?>
