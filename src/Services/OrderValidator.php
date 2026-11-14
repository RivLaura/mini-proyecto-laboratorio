<?php
/**
 * Validacion de los datos de entrada de un pedido.
 *
 * Mismas reglas y mensajes que tenia procesarPedidoCompleto(), pero en
 * lugar de imprimir HTML y cortar con return, lanza una excepcion para
 * que la capa de presentacion decida como mostrar el error.
 */
class OrderValidator
{
    /** @throws InvalidArgumentException */
    public function validate(string $paciente, float $monto): void
    {
        if (trim($paciente) === '') {
            throw new InvalidArgumentException('Falta el paciente');
        }

        if ($monto <= 0) {
            throw new InvalidArgumentException('Monto invalido');
        }
    }
}
