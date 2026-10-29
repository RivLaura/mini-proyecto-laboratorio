<?php
/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS
 *  Patron esperado: SINGLETON (creacional)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA
 *     1. obtener() crea una conexion NUEVA en cada llamada.
 *     2. Las credenciales llegan por constantes globales definidas en index.php.
 *     3. La excepcion se traga en silencio y el sistema sigue como si nada.
 *
 *  ✅ FORMA CORRECTA
 *     1. Singleton: constructor privado + instancia estatica + getInstance().
 *     2. La configuracion se inyecta, no se lee de constantes globales.
 *     3. Si la conexion falla, el error se propaga: fallar rapido y fuerte.
 *
 *  ⚠️ Advertencia de la clase: Singleton sirve para conexion, logger y
 *     configuracion. NO para guardar el pedido actual ni el usuario logueado.
 *     Ahi deja de ser un patron y pasa a ser una variable global disfrazada.
 * ============================================================================
 */

class Connection
{
    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    private static array $tablaEnMemoria = [];

    /** Instancia única de la conexión. */
    private static ?Connection $instance = null;

    /**
     * Constructor privado para impedir que se creen instancias
     * directamente desde fuera de la clase.
     */
    private function __construct()
    {
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new LogicException(
            'No se permite deserializar la conexión.'
        );
    }

    /**
     * Devuelve la única instancia de Connection.
     */
    public static function obtener(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }

        return self::$instance;
    }

    public function ejecutar(string $sql): void
    {
        try {
            self::$tablaEnMemoria[] = $sql;
        } catch (Throwable $e) {
            throw new RuntimeException(
                'No se pudo ejecutar la operación.',
                0,
                $e
            );
        }
    }

    public function consultas(): array
    {
        return self::$tablaEnMemoria;
    }
}