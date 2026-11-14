
# Diagnóstico y resolución de deudas técnicas — Laboratorio de Pedidos

## 1. Introducción

El presente documento identifica y analiza las deudas técnicas del proyecto académico Laboratorio de Pedidos, desarrollado para la asignatura Metodología de Sistemas II de la Tecnicatura Universitaria en Programación (UTN).

El proyecto original presentaba problemas relacionados con la duplicación de código, el acoplamiento entre componentes, la concentración de responsabilidades y las dificultades para realizar modificaciones y pruebas.

Estas problemáticas fueron abordadas mediante la aplicación de principios SOLID, patrones de diseño y prácticas de desarrollo colaborativo con Git y GitHub.

El análisis contempla dos categorías:

- **Deuda de proceso (Unidad 1):** problemas relacionados con la organización del trabajo, las revisiones de código y la integración de cambios.
- **Deuda de diseño (Unidad 2):** problemas relacionados con la estructura del código, las dependencias y la distribución de responsabilidades.

## 2. Metodología de análisis

Para identificar las deudas técnicas se examinó el código original del proyecto, considerando como referencia histórica el commit `c657de9`.

Las evidencias del diagnóstico inicial corresponden a dicha versión del código. Por este motivo, las referencias a archivos y números de línea deben interpretarse en relación con ese estado del repositorio.

Posteriormente, se analizaron las soluciones implementadas y su integración mediante Pull Requests.

La rama `integracion/entrega-tp` contiene la versión refactorizada, con los patrones de diseño, la arquitectura MVC y las pruebas automatizadas incorporadas.

El último estado integrado verificado corresponde al commit `f0a2cbe`.

Para cada deuda técnica se documentarán:

1. El síntoma observado en el proyecto original.
2. La evidencia técnica que permite identificarlo.
3. Su clasificación como deuda de proceso o de diseño.
4. El patrón o práctica aplicada para abordarlo.
5. La consecuencia negativa o limitación de la solución.

Esta información permitirá comparar el estado original del sistema con los resultados obtenidos después de la refactorización.

## 3. Identificación y análisis de deudas técnicas

La siguiente tabla presenta las principales deudas técnicas identificadas en la versión original del proyecto, correspondiente al commit `c657de9`.

Las referencias de archivos y líneas corresponden a dicha versión histórica del código, antes de la integración de los patrones.

Las soluciones indicadas fueron incorporadas posteriormente a través de los Pull Requests del equipo.

### 3.1. Matriz de deudas técnicas

| Síntoma observado | Evidencia (archivo:línea) | Tipo de deuda | Patrón o práctica aplicada | Consecuencia negativa asumida |
|---|---|---|---|---|
| Ausencia de revisiones cruzadas en los primeros Pull Requests. | PR #1 al #10: sin revisiones registradas. | Proceso (Unidad 1) | Code Review y aprobación cruzada de PR. | Las revisiones requieren coordinación entre integrantes y pueden prolongar el proceso de integración. |
| Implementaciones desarrolladas en ramas que inicialmente no estaban integradas. | PR #1 al #10: cerrados sin merge. Estado inicial de `integracion/entrega-tp` en `c657de9`. | Proceso (Unidad 1) | Integración progresiva mediante ramas y Pull Requests. | Pueden producirse conflictos entre ramas, por lo que se requiere coordinación y verificación continua. |
| Carga manual de clases, configuración en el código y rutas mediante condicionales. | `public/index.php:23-33`, `49-52`, `66-74`. | Diseño (Unidad 2) | Autoload, configuración externa y mapa de rutas. | Se incorporan archivos y mecanismos adicionales de configuración y carga de clases. |
| Creación de una nueva instancia de Connection en cada solicitud del método obtener(). | `src/Database/Connection.php:34-36`. | Diseño (Unidad 2) | Singleton. | Introduce estado compartido y requiere precauciones durante las pruebas y el mantenimiento. |
| El modelo Order contiene operaciones de persistencia y reglas de precios. | `src/Models/Order.php:62-68` y `96-108`. | Diseño (Unidad 2) | Repository, Strategy y SRP. | Se incrementa la cantidad de clases y dependencias que deben coordinarse. |
| Algoritmos de cálculo de precios concentrados en un switch y descuentos duplicados. | `src/Pricing/PriceCalculator.php:27-43` y `50-57`. | Diseño (Unidad 2) | Strategy. | Cada política de precios requiere una implementación y configuración específicas. |
| Creación de notificadores mediante condicionales repetidos y dependencia de clases concretas. | `src/Notifications/NotificationSender.php:30-44`. | Diseño (Unidad 2) | Factory. | Se centraliza la selección de notificadores, pero la fábrica debe mantenerse cuando se agregan nuevos tipos. |
| Modificación de un componente heredado y duplicación de su comportamiento. | `src/Legacy/LegacyNotifier.php:38-41` y `49-55`. | Diseño (Unidad 2) | Adapter. | Se agrega una clase intermedia que debe mantenerse compatible con ambas interfaces. |
| Generación de reportes mediante múltiples parámetros booleanos. | `src/Reports/ReportGenerator.php:28-49`. | Diseño (Unidad 2) | Decorator. | Aumenta la cantidad de objetos y el orden de los decoradores puede afectar el resultado. |
| Avisos del pedido dependientes directamente de implementaciones concretas. | `src/Events/OrderEvents.php:28-40`. | Diseño (Unidad 2) | Observer. | El flujo de notificaciones resulta menos evidente, por lo que se necesita documentar y probar los observadores registrados. |
| Concentración de validación, precios, persistencia, notificaciones y reportes en un servicio. | `src/Services/OrderService.php:34-89`. | Diseño (Unidad 2) | Facade y servicios especializados. | El procesamiento queda distribuido entre más componentes y requiere una configuración adecuada de dependencias. |
| Mezcla de SQL, cálculos y presentación HTML en el controlador, junto con lógica de negocio en la vista. | `src/Controllers/OrderController.php:30-53` y `views/orders.php:24,43,49`. | Diseño (Unidad 2) | MVC y SRP. | La separación de capas aumenta la cantidad de archivos y exige mantener contratos claros entre controlador, servicios y vistas. |

### 3.2. Interpretación de los resultados

El diagnóstico permitió identificar problemas tanto en la organización del trabajo colaborativo como en la estructura interna del código.

**Deudas de proceso:**

Inicialmente, las implementaciones se encontraban distribuidas en ramas individuales y los primeros Pull Requests carecían de revisiones registradas.

Posteriormente, el equipo reorganizó el proceso de integración mediante nuevas ramas, revisiones cruzadas y Pull Requests dirigidos a `integracion/entrega-tp`.

**Deudas de diseño:**

Se identificaron problemas de duplicación de código, acoplamiento entre componentes y mezcla de responsabilidades.

Estas situaciones fueron abordadas mediante patrones de diseño y la reorganización de la arquitectura.

La refactorización no eliminó toda la complejidad del sistema, sino que permitió distribuirla entre componentes especializados.

Las consecuencias negativas de cada solución fueron consideradas para evaluar las ventajas y los costos de aplicar los patrones.

Es importante destacar que la incorporación de un patrón de diseño no implica automáticamente la eliminación de todas las limitaciones del sistema.

## 4. Estado de resolución de las deudas técnicas

Las deudas identificadas fueron abordadas mediante implementaciones desarrolladas en ramas individuales y posteriormente integradas en `integracion/entrega-tp`.

La integración final se realizó mediante Pull Requests, lo que permite consultar las modificaciones incorporadas y su trazabilidad en GitHub.

### 4.1. Trazabilidad de las soluciones

| Deuda técnica | Solución implementada | Pull Request | Estado |
|---|---|---|---|
| Falta de revisiones cruzadas | Incorporación de revisiones y aprobaciones en el proceso de integración | PR #11 al #23 | Mejorada parcialmente |
| Ramas con cambios no integrados | Reorganización del trabajo e integración progresiva | PR #11 al #23 | Resuelta para las implementaciones finales |
| Carga manual, configuración y rutas | Autoload, configuración externa y mapa de rutas | PR #11 | Implementada |
| Creación de instancias de Connection | Singleton | PR #12 | Implementada |
| Persistencia acoplada al modelo | Repository | PR #13 | Implementada con limitaciones |
| Reglas de precios duplicadas | Strategy | PR #14 | Implementada |
| Creación de notificadores acoplada | Factory | PR #15 | Implementada |
| Avisos dependientes de clases concretas | Observer | PR #16 | Implementada |
| Incompatibilidad con notificador heredado | Adapter | PR #17 | Implementada |
| Reportes con parámetros booleanos | Decorator | PR #18 | Implementada |
| Servicio con responsabilidades mezcladas | Facade y servicios especializados | PR #19 | Implementada |
| Mezcla de lógica y presentación | MVC | PR #21 | Implementada |

Además, el equipo incorporó pruebas automatizadas e integración continua mediante el PR #20.

Posteriormente, se actualizó el README mediante el PR #22 y se modernizó el workflow de GitHub Actions mediante el PR #23.

### 4.2. Limitaciones técnicas que permanecen

Aunque se resolvieron las principales deudas de diseño previstas para el trabajo práctico, el proyecto conserva algunas limitaciones.

**Persistencia simulada**

La clase `Connection` utiliza una estructura en memoria para registrar las operaciones ejecutadas.

El patrón Repository separa la responsabilidad de persistencia, pero la aplicación todavía no utiliza una base de datos real.

Además, `OrderRepository` construye una sentencia SQL mediante interpolación de valores. Si se implementara una base de datos real, sería necesario reemplazar este mecanismo por consultas parametrizadas para evitar riesgos de inyección SQL.

**Listado de pedidos**

La opción de listado presenta datos de demostración definidos en el controlador.

Actualmente no recupera registros persistidos en una base de datos.

**Notificaciones**

Las notificaciones de Email, SMS y WhatsApp son simulaciones utilizadas para comprobar el funcionamiento de los patrones.

No se realizan envíos mediante servicios externos de mensajería.

**Generación de reportes**

El patrón Decorator permite componer características adicionales sobre un reporte básico.

Sin embargo, el resultado es una representación textual. No se generan archivos PDF físicos ni firmas digitales criptográficas.

**Configuración de componentes**

La arquitectura utiliza servicios especializados y dependencias configuradas desde `OrderServiceFactory`.

Esta separación mejora la organización, pero también incrementa la cantidad de clases y requiere mantener correctamente sus relaciones.

**Proceso de revisión**

Durante la integración final se incorporaron revisiones de código y comprobaciones automatizadas.

Sin embargo, no todos los Pull Requests registran necesariamente una aprobación cruzada formal. Por lo tanto, la mejora del proceso de revisión no debe presentarse como un cumplimiento absoluto de ese criterio.

### 4.3. Resultado de la integración

El equipo completó la integración de los patrones de diseño y la arquitectura MVC en la rama `integracion/entrega-tp`.

Se incorporó PHPUnit para verificar el comportamiento esperado de los componentes y GitHub Actions para automatizar la ejecución de las pruebas.

En la última validación realizada se obtuvieron los siguientes resultados:

- 119 pruebas automatizadas aprobadas.
- 223 assertions exitosas.
- Ejecución satisfactoria de GitHub Actions.
- Pruebas funcionales de creación, listado y generación de reportes.
- Validación de solicitudes incorrectas mediante HTTP 400.
- Manejo de rutas inexistentes mediante HTTP 404.

El último commit de integración verificado para esta etapa corresponde a `f0a2cbe`.

Estos resultados demuestran que las funcionalidades evaluadas conservan el comportamiento esperado después de la integración colaborativa.

No obstante, las pruebas aprobadas no implican que se hayan eliminado todas las limitaciones técnicas ni que la aplicación esté preparada para un entorno productivo.

## 5. Conclusión del diagnóstico de deuda técnica

El análisis del proyecto *Laboratorio de Pedidos* permitió identificar deudas técnicas relacionadas tanto con la organización del proceso de desarrollo como con la estructura y el diseño del software.

En la versión original se observaron problemas de duplicación de código, alto acoplamiento, concentración de responsabilidades y dificultades para incorporar nuevas funcionalidades sin modificar múltiples componentes.

Para abordar estas problemáticas, el equipo realizó un proceso de refactorización mediante la aplicación de principios SOLID, patrones de diseño y buenas prácticas de desarrollo colaborativo.

Se implementaron los patrones Singleton, Factory, Repository, Strategy, Observer, Adapter, Decorator y Facade, junto con la reorganización de la aplicación mediante la arquitectura MVC.

Estas soluciones permitieron mejorar la distribución de responsabilidades, reducir la duplicación de reglas de negocio y facilitar la extensión y verificación de los componentes.

En relación con la deuda de proceso, el uso de Git y GitHub permitió reorganizar el trabajo mediante ramas individuales, Pull Requests, revisiones de código e integración progresiva. Las implementaciones finales quedaron documentadas mediante los Pull Requests #11 al #23, fusionados en la rama `integracion/entrega-tp`.

La incorporación de PHPUnit y GitHub Actions proporcionó mecanismos de verificación automatizada. En la validación final se aprobaron **119 pruebas y 223 assertions**, sin fallos, tanto en los entornos locales evaluados como en la integración continua.

No obstante, se identificaron limitaciones que permanecen fuera del alcance de la refactorización académica, principalmente la persistencia simulada, las notificaciones de demostración, la generación textual de reportes y la necesidad de utilizar consultas parametrizadas ante una futura implementación de base de datos real.

Por lo tanto, la resolución de las deudas técnicas debe comprenderse como una mejora del diseño y la mantenibilidad del sistema, y no como la eliminación absoluta de toda complejidad o riesgo técnico.

**En conclusión, el trabajo permitió aplicar patrones de diseño para resolver problemas concretos del código original, mejorar la organización del proceso colaborativo y comprobar el funcionamiento de las soluciones integradas mediante pruebas automatizadas.**