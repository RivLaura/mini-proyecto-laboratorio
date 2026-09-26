<?php
/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patron aplicado: OBSERVER (comportamiento)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, esta clase creaba y ejecutaba directamente cada notificacion.
 *
 *  SOLUCION
 *     OrderEvents actua como SUBJECT.
 *     Mantiene una coleccion de observadores que implementan OrderObserver.
 *
 *     El Subject conoce la INTERFAZ, no las clases concretas.
 *
 *  CONSECUENCIA NEGATIVA
 *     Ya no se puede saber desde esta clase, de forma directa, quienes
 *     reciben el evento.
 *
 *     Se pierde trazabilidad estatica, pero se gana desacoplamiento.
 *     Puede compensarse con nombres explicitos, logs y tests.
 * ============================================================================
 */


/**
 * ============================================================================
 *  INTERFAZ OBSERVER
 * ============================================================================
 */

interface OrderObserver
{
    public function update(Order $order): void;
}


/**
 * ============================================================================
 *  OBSERVADORES CONCRETOS
 * ============================================================================
 */

final class EmailObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "Email enviado para el pedido {$order->id}<br>";
    }
}


final class DashboardObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[DASHBOARD] actualizado para el pedido {$order->id}<br>";
    }
}


/**
 * ============================================================================
 *  EJERCICIO 5 — SMS OBSERVER
 * ============================================================================
 *
 * Se agrega un nuevo observador implementando la misma interfaz.
 *
 * OrderEvents NO necesita ser modificado.
 */

final class SmsObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "SMS enviado para el pedido {$order->id}<br>";
    }
}


/**
 * ============================================================================
 *  SUBJECT
 * ============================================================================
 *
 * OrderEvents cumple el rol de Subject.
 *
 * Importante:
 *     Esta clase solo conoce OrderObserver.
 *     No conoce EmailObserver, DashboardObserver ni SmsObserver.
 */

class OrderEvents
{
    /**
     * Coleccion de observadores.
     *
     * @var OrderObserver[]
     */
    private array $observers = [];


    /**
     * Agrega un observador.
     */
    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }


    /**
     * Notifica a todos los observadores.
     *
     * POLIMORFISMO:
     * cada observador implementa update(), pero cada uno puede
     * ejecutar una accion diferente.
     */
    public function pedidoCreado(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);
        }
    }
}