# Documentación de casos de prueba — Laboratorio de Pedidos

## 1. Introducción

El presente documento describe los casos de prueba automatizados implementados en el proyecto *Laboratorio de Pedidos*, desarrollado para la asignatura Metodología de Sistemas II de la Tecnicatura Universitaria en Programación (UTN).

Las pruebas tienen como finalidad verificar el comportamiento de los patrones de diseño, la arquitectura MVC y las funcionalidades principales después de la refactorización e integración colaborativa.

Para su implementación se utilizó **PHPUnit 11**, ejecutado tanto en los entornos locales de desarrollo como mediante integración continua con GitHub Actions.

Los archivos de pruebas se encuentran en el directorio `tests/` del repositorio.

## 2. Entorno de ejecución

| Componente | Configuración |
|---|---|
| Lenguaje | PHP 8.4 |
| Framework de pruebas | PHPUnit 11 |
| Gestor de dependencias | Composer |
| Configuración | `phpunit.xml.dist` |
| Preparación del entorno | `tests/bootstrap.php` |
| Directorio de pruebas | `tests/` |
| Integración continua | `.github/workflows/ci.yml` |
| Rama evaluada | `integracion/entrega-tp` |

### Ejecución local

En Windows (PowerShell):

`.\vendor\bin\phpunit.bat`

En Linux (Ubuntu):

`./vendor/bin/phpunit`

### Resultado de la última validación

- **119 pruebas aprobadas.**
- **223 assertions exitosas.**
- **0 fallos registrados.**
- Ejecución satisfactoria en Windows, Ubuntu y GitHub Actions.

Estos resultados corresponden a la versión integrada validada mediante el commit `f0a2cbe`.

## 3. Inventario de pruebas automatizadas

La suite de PHPUnit contiene archivos especializados que permiten verificar diferentes componentes del proyecto.

| Archivo de prueba | Componente evaluado | Objetivo principal |
|---|---|---|
| `tests/AutoloadTest.php` | Autoload | Comprobar la carga de clases y el tratamiento de clases inexistentes. |
| `tests/SingletonTest.php` | Singleton | Verificar la instancia única, las restricciones de creación y la conservación de consultas en memoria. |
| `tests/RepositoryTest.php` | Repository | Comprobar el registro simulado de pedidos y la separación de persistencia respecto del modelo. |
| `tests/StrategyTest.php` | Strategy | Verificar las estrategias de precios, descuentos configurables y validación de descuentos inválidos. |
| `tests/FactoryTest.php` | Factory | Comprobar la creación de notificadores, sus tipos admitidos y el rechazo de tipos desconocidos. |
| `tests/ObserverTest.php` | Observer | Verificar la suscripción, ejecución y tratamiento de errores de los observadores. |
| `tests/AdapterTest.php` | Adapter | Comprobar la compatibilidad entre la interfaz Notification y el componente heredado. |
| `tests/DecoratorTest.php` | Decorator | Verificar la composición, las combinaciones y el orden de los decoradores de reportes. |
| `tests/FacadeTest.php` | Facade | Comprobar la coordinación de validación, precios, persistencia, notificaciones, eventos y reportes. |
| `tests/MvcTest.php` | MVC | Verificar el controlador, las vistas, el escape de HTML y las respuestas ante errores. |
| `tests/RoutesTest.php` | Enrutamiento | Comprobar las rutas de creación, listado, reporte y acciones inexistentes. |
| `tests/ExampleTest.php` | Infraestructura de pruebas | Comprobar la ejecución básica de PHPUnit y la instanciación de clases. |

## 4. Matriz de casos de prueba

En esta sección se detallarán los casos de prueba más representativos, incluyendo su identificador, escenario, resultado esperado y archivo PHPUnit que realiza la comprobación.

Los casos se organizarán por componentes para facilitar su consulta y trazabilidad durante la evaluación académica.

### 4.1. Casos de prueba representativos

La siguiente matriz documenta los principales escenarios verificados mediante PHPUnit.

Cada caso incluye un identificador, el escenario evaluado, el resultado esperado y el método automatizado que permite comprobarlo.

| ID | Escenario de prueba | Resultado esperado | Archivo y método PHPUnit |
|---|---|---|---|
| CP-01 | Cargar clases desde diferentes carpetas del proyecto. | Las clases se cargan correctamente mediante el mecanismo de autoload. | `AutoloadTest.php` — `testCargaClasesDeDistintasCarpetas` |
| CP-02 | Solicitar dos veces una instancia de Connection. | Ambas referencias corresponden a la misma instancia. | `SingletonTest.php` — `testObtenerDevuelveSiempreLaMismaInstancia` |
| CP-03 | Intentar clonar la instancia de Connection. | La operación es rechazada mediante un error. | `SingletonTest.php` — `testNoPermiteClonarLaConexion` |
| CP-04 | Guardar un pedido mediante OrderRepository. | Se registra una nueva consulta SQL en la simulación de memoria. | `RepositoryTest.php` — `testGuardarPedidoRegistraLaConsulta` |
| CP-05 | Comprobar que el modelo Order no realice directamente la persistencia. | La clase Order no contiene el método `guardar()`. | `RepositoryTest.php` — `testModeloOrderNoTieneMetodoGuardar` |
| CP-06 | Calcular el precio de un paciente con obra social sobre $10.000. | Se obtiene $7.000 sin IVA y $8.470 con IVA, aplicando el 30 % de descuento. | `StrategyTest.php` — `testObraSocialConDescuentoDel30PorCiento` |
| CP-07 | Aplicar un descuento del 20 % a un paciente con prepaga sobre $10.000. | Se obtiene $8.000 sin IVA y $9.680 con IVA. | `StrategyTest.php` — `testPrepagaConDescuentoDel20PorCiento` |
| CP-08 | Configurar un descuento inválido para obra social. | Se lanza una excepción `InvalidArgumentException`. | `StrategyTest.php` — `testObraSocialRechazaDescuentosInvalidos` |
| CP-09 | Solicitar la creación de notificadores válidos. | Factory devuelve una implementación compatible con la interfaz Notification y el tipo solicitado. | `FactoryTest.php` — `testFactoryCreaNotificacionCorrecta` |
| CP-10 | Solicitar un notificador de tipo desconocido, como fax. | Se lanza una excepción `InvalidArgumentException`. | `FactoryTest.php` — `testFactoryRechazaTipoDesconocido` |
| CP-11 | Generar un evento con los observadores Email, SMS y Dashboard registrados. | Los tres observadores reciben el evento y producen sus avisos correspondientes. | `ObserverTest.php` — `testLosTresObservadoresRecibenElEvento` |
| CP-12 | Provocar un fallo en uno de los observadores. | Los demás observadores continúan ejecutándose y el fallo se informa mediante una excepción. | `ObserverTest.php` — `testSiUnObservadorFallaLosRestantesSiguenEjecutandose` |
| CP-13 | Enviar un mensaje mediante LegacyNotifierAdapter. | El adaptador delega la operación una vez al método `sendMessage()` del componente heredado. | `AdapterTest.php` — `testSendDelegaEnSendMessageDelSistemaHeredado` |
| CP-14 | Generar reportes con diferentes combinaciones de decoradores. | Las ocho combinaciones producen las representaciones textuales esperadas. | `DecoratorTest.php` — `testLasOchoCombinacionesProducenElResultadoEsperado` |
| CP-15 | Modificar el orden de aplicación de los decoradores. | El orden modifica la representación textual del reporte. | `DecoratorTest.php` — `testElOrdenDeLosDecoradoresModificaElResultado` |
| CP-16 | Ejecutar el procesamiento completo de un pedido mediante Facade. | Los servicios se ejecutan en el orden: validar, calcular, guardar, notificar, avisar y generar reporte. | `FacadeTest.php` — `testRespetaElOrdenDeEjecucionDeLosServicios` |
| CP-17 | Procesar pedidos con paciente vacío o monto inválido. | Se rechaza la operación mediante `InvalidArgumentException`. | `FacadeTest.php` — `testRechazaPedidosInvalidos` |
| CP-18 | Crear un pedido de $20.000 para un paciente con obra social mediante MVC. | La salida muestra el nombre del paciente y el total calculado de $14.000. | `MvcTest.php` — `testResultadoMuestraPacienteYTotal` |
| CP-19 | Ingresar caracteres HTML en el nombre del paciente. | Los caracteres se muestran escapados y no aparecen como etiquetas HTML sin procesar. | `MvcTest.php` — `testEscapeHtmlEnNombrePaciente` |
| CP-20 | Solicitar la creación de un pedido sin nombre de paciente. | Se muestra una respuesta de validación controlada con código HTTP 400, sin error fatal. | `MvcTest.php` — `testEntradaInvalidaNoDevuelveError500` |
| CP-21 | Solicitar una acción inexistente. | El sistema devuelve HTTP 404 y el mensaje «Accion no encontrada». | `RoutesTest.php` — `testAccionInexistente` |
| CP-22 | Comprobar el funcionamiento básico de la infraestructura de PHPUnit. | La aserción de prueba se ejecuta correctamente. | `ExampleTest.php` — `testLaInfraestructuraDePhpUnitFunciona` |

### 4.2. Alcance de la matriz

Los casos anteriores constituyen una selección representativa de las comprobaciones automatizadas.

La suite completa incluye pruebas adicionales sobre validaciones, integración entre componentes, manejo de errores, extensibilidad y comportamiento de las rutas.

Algunos métodos utilizan proveedores de datos (*Data Providers*) que permiten ejecutar diferentes escenarios a partir de una misma implementación de prueba.

Por este motivo, la cantidad de métodos identificados en los archivos no debe confundirse con el número total de pruebas ejecutadas por PHPUnit.

Para consultar la implementación completa de cada escenario, se debe acceder al archivo correspondiente dentro del directorio `tests/`.

### 4.3. Resultados obtenidos

La última ejecución verificada de la suite completa finalizó correctamente:

- Pruebas ejecutadas: 119.
- Pruebas aprobadas: 119.
- Assertions exitosas: 223.
- Fallos detectados: 0.

Estos resultados se comprobaron en los entornos locales de Windows y Ubuntu, así como mediante GitHub Actions.

La aprobación de las pruebas demuestra que los escenarios evaluados cumplen sus resultados esperados, aunque no constituye una garantía de ausencia absoluta de defectos.

## 5. Evidencias de ejecución y trazabilidad

Las pruebas automatizadas fueron verificadas mediante ejecuciones locales y un proceso de integración continua configurado con GitHub Actions.

### 5.1. Ejecución local en Windows

La suite fue ejecutada desde PowerShell utilizando PHP 8.4.23 y PHPUnit 11.5.58.

Comando utilizado:

```powershell
.\vendor\bin\phpunit.bat
```

Resultado obtenido:

`OK (119 tests, 223 assertions)`

Esta ejecución permitió comprobar el funcionamiento de las pruebas automatizadas en el entorno local de desarrollo.

### 5.2. Ejecución local en Ubuntu

La suite también fue ejecutada en Ubuntu mediante PHP 8.4.27 y PHPUnit 11.5.58.

Comando utilizado:

```bash
./vendor/bin/phpunit
```

Resultado obtenido:

`OK (119 tests, 223 assertions)`

La ejecución se realizó después de actualizar la rama `integracion/entrega-tp` y verificar las dependencias instaladas mediante Composer.

### 5.3. Integración continua con GitHub Actions

El proyecto utiliza GitHub Actions para ejecutar automáticamente las pruebas de PHPUnit.

El workflow se encuentra en:

`.github/workflows/ci.yml`


La ejecución exitosa verificada corresponde al commit `f0a2cbe` de la rama `integracion/entrega-tp`, antes de incorporar los documentos de esta entrega.

**Enlace a la ejecución exitosa:**

https://github.com/RivLaura/mini-proyecto-laboratorio/actions/runs/43623449354

Resultados registrados:

- Estado: Success.
- PHP: 8.4.27.
- PHPUnit: 11.5.58.
- Pruebas aprobadas: 119.
- Assertions exitosas: 223.
- Fallos: 0.

Esta ejecución demuestra que la suite automatizada finalizó satisfactoriamente en el entorno de integración continua.

### 5.4. Ubicación de las evidencias

Las implementaciones de los casos de prueba se encuentran en el directorio:

`tests/`

La documentación de los escenarios, resultados esperados y métodos PHPUnit se encuentra en:

`docs/CASOS-DE-PRUEBA.md`

El diagnóstico de deudas técnicas se encuentra en:

`docs/DEUDA-TECNICA.md`

El historial de Pull Requests y las ejecuciones de GitHub Actions permiten consultar la integración de los cambios y los resultados de las pruebas.

---

## 6. Conclusión de las pruebas

La incorporación de PHPUnit y GitHub Actions permitió establecer mecanismos de verificación automatizada para los principales componentes del sistema Laboratorio de Pedidos.

Las pruebas abarcaron los patrones de diseño, la arquitectura MVC, el procesamiento de pedidos, el manejo de errores y la integración entre servicios.

La suite obtuvo resultados satisfactorios en Windows, Ubuntu y GitHub Actions, con 119 pruebas aprobadas y 223 assertions exitosas.

Estos resultados proporcionan evidencia de que los componentes evaluados mantienen el comportamiento esperado después de la refactorización.

Sin embargo, la aprobación de las pruebas no garantiza la ausencia absoluta de defectos ni reemplaza las verificaciones necesarias para un entorno productivo.

En conclusión, la automatización de pruebas y la integración continua contribuyeron a mejorar la capacidad de verificación, la mantenibilidad y la confiabilidad del proceso de desarrollo colaborativo.
