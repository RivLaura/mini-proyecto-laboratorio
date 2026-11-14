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
    // ❌ MAL APLICADO: estado publico y mutable.
    //    $order->amount = -50000;  es valido y rompe el sistema en silencio.
    public int $id;
    public string $patient;
    public float $amount;
    public string $patientType;

    public function __construct(int $id, string $patient, float $amount, string $patientType)
    {
        // ❌ MAL APLICADO: sin ninguna validacion. El objeto puede nacer invalido.
        $this->id          = $id;
        $this->patient     = $patient;
        $this->amount      = $amount;
        $this->patientType = $patientType;
    }

    /*
     * ✅ FORMA CORRECTA:
     *
     * public function __construct(
     *     public readonly int $id,
     *     public readonly string $patient,
     *     public readonly float $amount,
     *     public readonly string $patientType
     * ) {
     *     if ($amount < 0) {
     *         throw new InvalidArgumentException('El importe no puede ser negativo');
     *     }
     *     if (trim($patient) === '') {
     *         throw new InvalidArgumentException('El paciente es obligatorio');
     *     }
     * }
     */


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

    /**
     * ❌ METODO MAL APLICADO: calcularTotal()
     *    Cadena de if con las reglas de precio de TODOS los tipos de paciente.
     *    Agregar "prepaga" obliga a modificar el modelo => viola Abierto/Cerrado.
     */
    public function calcularTotal(): float
    {
        if ($this->patientType === 'particular') {
            return $this->amount;
        }

        if ($this->patientType === 'obra_social') {
            return $this->amount * 0.7;
        }

        // ❌ Y aca alguien va a pegar el proximo if. Y el siguiente.
        return $this->amount;
    }

    /*
     * ✅ FORMA CORRECTA: delegar en una estrategia.
     *
     * $total = (new PriceCalculator(new InsuranceStrategy()))->calculate($order->amount);
     */
}
