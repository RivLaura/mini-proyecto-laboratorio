<?php
/**
 * ============================================================================
 *  VISTA: error de validacion
 * ============================================================================
 *
 *  Vista MVC: recibe $error ya escapado por el controlador
 *  y solo se limita a mostrarlo.
 *
 *  No contiene consultas a la base de datos, ni calculos de descuentos,
 *  ni logica de negocio.
 */

declare(strict_types=1);
?>
<h1>Error de validacion</h1>
<p><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
