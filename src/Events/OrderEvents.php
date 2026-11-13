<?php
/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patron aplicado: OBSERVER (comportamiento)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, pedidoCreado() creaba y ejecutaba a mano cada aviso
 *     (EmailNotification, SmsNotification y el dashboard). Sumar un aviso
 *     obligaba a modificar esta clase y, si uno fallaba, se cortaba la cadena.
 *
 *  SOLUCION
 *     OrderEvents actua como SUBJECT.
 *     Mantiene una coleccion de observadores que implementan OrderObserver
 *     y les avisa por polimorfismo. El Subject conoce la INTERFAZ, no las
 *     clases concretas (EmailObserver, DashboardObserver, SmsObserver).
 *
 *     Si un observador falla, el resto igual recibe el evento y al final
 *     se informa el error: no se corta la cadena ni se oculta el fallo.
 *
 *  CONSECUENCIA NEGATIVA
 *     Leyendo el codigo del pedido ya no se ve quien se entera del evento.
 *     Se pierde trazabilidad estatica, pero se gana desacoplamiento.
 *     Se compensa con nombres explicitos en cada observador, con el
 *     registro centralizado en un unico lugar (quien arma los objetos)
 *     y con pruebas que verifican que todos reciben el evento.
 * ============================================================================
 */

class OrderEvents
{
    /** @var OrderObserver[] */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * Notifica a todos los observadores registrados.
     *
     * Cada observador se ejecuta aunque uno anterior haya fallado.
     * Si hubo fallos, se lanza una unica excepcion al final con el detalle
     * de todos ellos (la primera excepcion queda como "previous").
     *
     * @throws RuntimeException si al menos un observador fallo.
     */
    public function pedidoCreado(Order $order): void
    {
        $errores = [];

        foreach ($this->observers as $observer) {
            try {
                $observer->update($order);
            } catch (Throwable $error) {
                $errores[] = $error;
            }
        }

        if ($errores !== []) {
            $detalle = array_map(
                fn(Throwable $e): string => $e->getMessage(),
                $errores
            );

            throw new RuntimeException(
                sprintf(
                    'Fallaron %d observador(es) del pedido %d: %s',
                    count($errores),
                    $order->id,
                    implode('; ', $detalle)
                ),
                0,
                $errores[0]
            );
        }
    }
}
