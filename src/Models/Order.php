<?php
/**
 * ============================================================================
 *  MODELO Order
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     1. Propiedades públicas mutables: cualquiera puede dejar el objeto invalido.
 *     2. El modelo se persiste a si mismo (guardar()): mezcla dominio y datos.
 *     3. El modelo calcula el precio segun el tipo de paciente: reglas de negocio
 *        que cambian por otro motivo, dentro de la misma clase.
 *
 *  ✅ FORMA CORRECTA
 *     1. Propiedades readonly y validacion en el constructor (encapsulamiento).
 *     2. La persistencia va a un OrderRepository (una responsabilidad, SRP).
 *     3. El calculo de precio sale a PricingStrategy (ver src/Pricing).
 * ============================================================================
 */

class Order
{
    /**
     * ✅ Propiedades readonly: solo se asignan en el constructor y no pueden
     *    modificarse desde fuera. Compatible con los consumidores existentes
     *    que leen $order->id, $order->patient, $order->amount, $order->patientType.
     */
    public readonly int $id;
    public readonly string $patient;
    public readonly float $amount;
    public readonly string $patientType;

    public function __construct(int $id, string $patient, float $amount, string $patientType)
    {
        $this->id          = $id;
        $this->patient     = $patient;
        $this->amount      = $amount;
        $this->patientType = $patientType;
    }

    /*
     * ✅ FORMA CORRECTA: sacar la persistencia del modelo.
     *
     * final class OrderRepository
     * {
     *     public function __construct(private PDO $pdo) {}
     *
     *     public function save(Order $order): void
     *     {
     *         $stmt = $this->pdo->prepare(
     *             'INSERT INTO orders (id, patient, amount) VALUES (:id, :patient, :amount)'
     *         );
     *         $stmt->execute([
     *             'id'      => $order->id,
     *             'patient' => $order->patient,
     *             'amount'  => $order->amount,
     *         ]);
     *     }
     * }
     */
}
