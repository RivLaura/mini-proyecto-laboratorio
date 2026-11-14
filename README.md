
# Laboratorio de Pedidos — Integración de Patrones de Diseño

## Descripción del proyecto

Proyecto académico desarrollado para la asignatura **Metodología de Sistemas II**, correspondiente a la **Tecnicatura Universitaria en Programación de la Universidad Tecnológica Nacional (UTN)**.

El sistema representa la gestión de pedidos de un laboratorio de análisis clínicos y está desarrollado en **PHP**, utilizando programación orientada a objetos.

El proyecto original contenía deudas técnicas introducidas con fines educativos. El objetivo del trabajo colaborativo fue identificar, analizar y refactorizar dichas deudas mediante la aplicación de principios SOLID, patrones de diseño y buenas prácticas de desarrollo de software.

## Objetivos

- Aplicar patrones de diseño a problemas concretos de mantenimiento y extensibilidad.
- Separar las responsabilidades de los componentes del sistema.
- Reducir el acoplamiento y la duplicación de código.
- Mejorar la organización mediante la arquitectura MVC.
- Implementar pruebas automatizadas con PHPUnit.
- Utilizar Git y GitHub para el desarrollo colaborativo, la revisión de código y la integración continua.

## Tecnologías utilizadas

- PHP 8.4.
- Programación orientada a objetos.
- Composer para la gestión de dependencias.
- PHPUnit 11 para pruebas automatizadas.
- Git y GitHub para el control de versiones.
- GitHub Actions para integración continua.
- HTML para la presentación de las vistas.

## Alcance del proyecto

El sistema permite demostrar el procesamiento de pedidos, el cálculo de precios según el tipo de paciente, las notificaciones, los eventos y la generación de reportes mediante patrones de diseño.

La persistencia se encuentra simulada en memoria y el listado presenta datos de demostración. No se requiere una base de datos MySQL para ejecutar las pruebas y la aplicación.

---


## Instalación y ejecución

### Requisitos previos

Para ejecutar el proyecto se necesita:

- PHP 8.4.x o posterior dentro de la serie 8.x (Composer requiere ^8.4).
- Composer 2.x.
- Git.
- Un navegador web.

No es obligatorio utilizar XAMPP ni instalar MySQL, ya que el proyecto utiliza una simulación de persistencia en memoria.

### 1. Clonar el repositorio

Abrir una terminal y ejecutar:

```bash
git clone --branch integracion/entrega-tp https://github.com/RivLaura/mini-proyecto-laboratorio.git
cd mini-proyecto-laboratorio
```

La rama `integracion/entrega-tp` contiene los patrones de diseño y la arquitectura MVC integrados por el equipo.

### 2. Instalar las dependencias

Ejecutar desde la raíz del proyecto:

```bash
composer install
```

Composer instalará las dependencias definidas en `composer.lock`, incluyendo PHPUnit para las pruebas automatizadas.

### 3. Preparar la configuración local

El proyecto incluye el archivo:

`config/database.example.php`

Se debe crear una copia llamada `config/database.php`.

**En Windows (PowerShell):**

```powershell
Copy-Item config/database.example.php config/database.php
```

**En Linux (Bash):**

```bash
cp config/database.example.php config/database.php
```

El archivo `config/database.php` no debe incorporarse al control de versiones.

No es necesario configurar credenciales de MySQL para esta demostración, porque la persistencia está simulada.

### 4. Iniciar el servidor PHP

Desde la raíz del proyecto, ejecutar:

```bash
php -S 127.0.0.1:8000 -t public
```

Mantener abierta la terminal mientras se utiliza la aplicación.

Abrir las siguientes direcciones en un navegador:

| Acción | Dirección |
|---|---|
| Crear pedido | http://127.0.0.1:8000/index.php?accion=crear |
| Listar pedidos | http://127.0.0.1:8000/index.php?accion=listar |
| Generar reporte | http://127.0.0.1:8000/index.php?accion=reporte |

Para detener el servidor, presionar `Ctrl + C`.

### 5. Ejecutar las pruebas automatizadas

El proyecto utiliza PHPUnit 11.

**En Windows (PowerShell):**

```powershell
.\vendor\bin\phpunit.bat
```

**En Linux (Bash):**

```bash
./vendor/bin/phpunit
```

La suite de pruebas verifica el funcionamiento de los patrones implementados, la arquitectura MVC y las rutas principales del sistema.

**Último resultado verificado:**

- 119 pruebas aprobadas.
- 223 assertions exitosas.
- PHP 8.4.
- Sin errores detectados por PHPUnit.

### 6. Integración continua

El repositorio utiliza GitHub Actions mediante el workflow:

`.github/workflows/ci.yml`

El workflow ejecuta automáticamente las pruebas cuando se realizan cambios o Pull Requests dirigidos a `integracion/entrega-tp`.

El proceso incluye:

1. Obtener el código del repositorio.
2. Configurar PHP 8.4.
3. Instalar dependencias mediante Composer.
4. Preparar la configuración de pruebas.
5. Ejecutar PHPUnit.

La última ejecución posterior a la integración MVC finalizó correctamente.

### 7. Limitaciones de la demostración

Este proyecto tiene fines académicos.

- La persistencia utiliza una simulación en memoria.
- No se requiere una base de datos MySQL.
- El listado muestra datos de demostración preparados por el controlador.
- Las notificaciones se representan mediante mensajes simulados.
- Los decoradores generan una representación textual de los reportes; no crean archivos PDF reales.

Estas limitaciones permiten estudiar y comprobar los patrones de diseño sin depender de infraestructura externa.

---


## Mapa de deudas técnicas resueltas

El proyecto original contenía diferentes deudas técnicas relacionadas con el acoplamiento, la duplicación de código, la distribución inadecuada de responsabilidades y las dificultades para extender y mantener el sistema.

Para resolverlas, el equipo implementó patrones de diseño creacionales, estructurales, de comportamiento y el patrón arquitectónico MVC.

### Trazabilidad de las soluciones implementadas

| Deuda técnica identificada | Patrón o solución aplicada | Responsable | Pull Request |
|---|---|---|---|
| Carga manual de dependencias, configuración acoplada y enrutamiento mediante condicionales | Autoload, configuración externa y mapa de rutas | Laura | [PR #11](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/11) |
| Creación de múltiples instancias de conexión | Singleton | Laura | [PR #12](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/12) |
| Persistencia acoplada al modelo y consultas SQL dispersas | Repository | Laura | [PR #13](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/13) |
| Algoritmos de cálculo de precios duplicados y dependientes de condicionales | Strategy | Laura | [PR #14](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/14) |
| Creación de notificadores mediante condicionales y dependencia de clases concretas | Factory | Laura | [PR #15](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/15) |
| Acoplamiento entre la creación de pedidos y los mecanismos de aviso | Observer | Fernando | [PR #16](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/16) |
| Incompatibilidad entre la interfaz de notificación del proyecto y un componente heredado | Adapter | Fernando | [PR #17](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/17) |
| Generación de reportes mediante parámetros booleanos y combinaciones rígidas | Decorator | Fernando | [PR #18](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/18) |
| Concentración de múltiples responsabilidades en `OrderService` | Facade y principio de responsabilidad única (SRP) | Fernando | [PR #19](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/19) |
| Mezcla de lógica de negocio, persistencia y presentación en el controlador, modelo y vistas | MVC y SRP | Alejandro | [PR #21](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/21) |

### Infraestructura de pruebas e integración continua

Además de los patrones de diseño, se incorporaron pruebas automatizadas e integración continua.

**Responsable:** Alejandro.

**Pull Request:** [PR #20 — PHPUnit y GitHub Actions](https://github.com/RivLaura/mini-proyecto-laboratorio/pull/20).

Las pruebas automatizadas permiten verificar el comportamiento individual y la integración de los componentes.

GitHub Actions ejecuta PHPUnit automáticamente sobre los cambios dirigidos a la rama de integración.

**Último resultado verificado:**
- 119 pruebas aprobadas.
- 223 assertions exitosas.
- PHP 8.4.
- Integración continua aprobada.

### Resultado de la refactorización

La aplicación pasó de una estructura con responsabilidades mezcladas y lógica duplicada a una arquitectura con componentes especializados.

Los principales resultados fueron:

- Centralización de la persistencia mediante Repository.
- Encapsulamiento de la conexión mediante Singleton.
- Separación de algoritmos de precios mediante Strategy.
- Centralización de la creación de notificadores mediante Factory.
- Desacoplamiento de los avisos mediante Observer.
- Adaptación del componente heredado mediante Adapter.
- Composición flexible de reportes mediante Decorator.
- Coordinación del procesamiento de pedidos mediante Facade.
- Separación de modelo, controlador y vistas mediante MVC.

La integración se realizó mediante ramas individuales, Pull Requests, revisiones de código y pruebas automatizadas.

Todos los componentes fueron incorporados a la rama `integracion/entrega-tp`.

### Consideraciones de alcance

La refactorización tuvo como objetivo mejorar el diseño del sistema académico, manteniendo sus funcionalidades principales.

La aplicación conserva una persistencia simulada en memoria y notificaciones representadas mediante mensajes de demostración.

El patrón Adapter está disponible para integrar el notificador heredado, aunque no forma parte del flujo predeterminado de creación de pedidos.

La generación de reportes mediante Decorator produce una representación textual y no un documento PDF físico.

Estas características corresponden al alcance de la práctica y no deben confundirse con funcionalidades de un sistema productivo.

---

## Análisis de la deuda técnica: antes y después

### Situación inicial

El proyecto original presentaba una deuda técnica significativa: las reglas de cálculo de precios estaban duplicadas en diferentes componentes.

Por ejemplo, el descuento de obra social se aplicaba mediante el factor `0.7`, distribuido entre el modelo, el controlador, los servicios, el calculador de precios y las vistas.

Esto generaba varios problemas:

- Duplicación de reglas de negocio.
- Mayor dificultad para modificar descuentos.
- Riesgo de obtener resultados inconsistentes.
- Acoplamiento entre las capas de la aplicación.
- Mayor esfuerzo de mantenimiento y pruebas.

Si el porcentaje de descuento cambiaba, era necesario revisar múltiples archivos para garantizar que todos utilizaran la misma regla.

### Solución implementada

Se aplicó el patrón de comportamiento **Strategy** para encapsular los diferentes algoritmos de cálculo de precios.

Actualmente, las políticas de precios están representadas por clases especializadas:

- `PrivatePatientStrategy`: pacientes particulares.
- `InsuranceStrategy`: pacientes con obra social.
- `RetiredPatientStrategy`: pacientes jubilados.
- `PrepaidStrategy`: pacientes con prepaga y descuento configurable.

Todas implementan la interfaz `PricingStrategy`.

La clase `PriceCalculator` utiliza la estrategia recibida para realizar el cálculo, sin necesitar conocer las condiciones particulares de cada tipo de paciente.

Por su parte, `OrderPricingService` selecciona la estrategia correspondiente mediante una configuración proporcionada por `OrderServiceFactory`.

### Ejemplo: descuento de obra social

**Antes de la refactorización:**

El descuento se repetía mediante operaciones como:

```php id="hk40g4"
$total = $monto * 0.7;
```

La misma regla podía aparecer en diferentes componentes, generando múltiples puntos de mantenimiento.

**Después de la refactorización:**

La clase `InsuranceStrategy` encapsula el descuento:

```php id="sxq63u"
final class InsuranceStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.30)
    {
        if (!is_finite($discount) || $discount < 0 || $discount > 1) {
            throw new InvalidArgumentException(
                'El descuento debe estar entre 0 y 1.'
            );
        }
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}
```

El descuento predeterminado es del 30 %.

Para un importe de $15.000:

- Importe original: $15.000.
- Descuento aplicado: 30 %.
- Total resultante: $10.500.

Si la política de descuento cambia, la modificación se realiza en la configuración de la estrategia correspondiente, sin duplicar la regla en el controlador, el modelo o las vistas.

Las pruebas automatizadas que verifican los importes esperados también deben actualizarse cuando cambia una regla de negocio.

### Comparación de la arquitectura

| Aspecto | Antes | Después |
|---|---|---|
| Cálculo de precios | Condicionales y reglas duplicadas | Strategy |
| Persistencia | Acoplada a distintas capas | Repository |
| Conexión | Instanciación sin control centralizado | Singleton |
| Notificaciones | Selección mediante condicionales | Factory |
| Eventos | Avisos acoplados | Observer |
| Componentes heredados | Interfaces incompatibles | Adapter |
| Reportes | Parámetros booleanos | Decorator |
| Procesamiento de pedidos | Múltiples responsabilidades en un servicio | Facade |
| Presentación | Lógica mezclada con HTML | MVC |
| Verificación | Sin la suite automatizada integrada | PHPUnit y GitHub Actions |

### Beneficios obtenidos

La refactorización permitió:

1. Reducir la duplicación de código.
2. Separar responsabilidades.
3. Disminuir el acoplamiento entre componentes.
4. Facilitar la incorporación de nuevas estrategias y comportamientos.
5. Mejorar la capacidad de prueba de las clases.
6. Centralizar las reglas de negocio.
7. Simplificar el mantenimiento del proyecto.
8. Verificar automáticamente las integraciones del equipo.

### Consideraciones finales

La aplicación de patrones no elimina toda la complejidad del software: la organiza y distribuye entre componentes con responsabilidades definidas.

Esta separación puede aumentar la cantidad de clases y archivos, pero permite que los cambios se realicen de manera más localizada y verificable.

El resultado final conserva las funcionalidades principales del laboratorio académico y cuenta con pruebas automatizadas que respaldan las refactorizaciones realizadas.

---

## Metodología de trabajo colaborativo

El proyecto fue desarrollado colaborativamente por tres integrantes, utilizando Git y GitHub como herramientas de control de versiones y coordinación.

El trabajo se organizó mediante la identificación de deudas técnicas, la asignación de responsabilidades, la implementación de patrones de diseño y la integración progresiva de los cambios.

### Integrantes y responsabilidades

| Integrante | Responsabilidades |
|---|---|
| Laura Riveros | Autoload, Singleton, Repository, Strategy, Factory y revisión de integraciones |
| Fernando Nieva | Observer, Adapter, Decorator y Facade |
| Alejandro Quiroga | PHPUnit, GitHub Actions y refactorización MVC |

Cada integrante trabajó sobre componentes específicos, procurando conservar la compatibilidad con las implementaciones de sus compañeros.

### Flujo de trabajo con Git y GitHub

El proceso de desarrollo siguió las siguientes etapas:

1. Identificación de la deuda técnica en el código original.
2. Análisis de los patrones de diseño aplicables.
3. Creación de una rama de trabajo específica.
4. Implementación de la solución.
5. Ejecución de pruebas y comprobaciones de sintaxis.
6. Publicación de los cambios mediante commits y push.
7. Creación de un Pull Request hacia la rama de integración.
8. Revisión técnica del código.
9. Corrección de observaciones y conflictos cuando correspondía.
10. Verificación de GitHub Actions.
11. Aprobación y fusión del Pull Request.

Este procedimiento permitió incorporar gradualmente los patrones sin modificar directamente la rama de integración durante su desarrollo.


### Organización de ramas

Se utilizó la rama `integracion/entrega-tp` como destino de las implementaciones colaborativas.

Cada solución se desarrolló en una rama específica y se incorporó mediante un Pull Request.

Las ramas utilizadas para los Pull Requests finales fueron:

| Implementación | Rama de integración | PR |
|---|---|---|
| Autoload, configuración y rutas | `fix/autoload-integracion` | #11 |
| Singleton | `fix/singleton-integracion` | #12 |
| Repository | `fix/repository-integracion` | #13 |
| Strategy | `feat/strategy-core-integracion` | #14 |
| Factory | `feat/factory-core-integracion` | #15 |
| Observer | `fix/observer-integracion` | #16 |
| Adapter | `fix/adapter-integracion` | #17 |
| Decorator | `fix/decorator-integracion` | #18 |
| Facade | `fix/facade-integracion` | #19 |
| PHPUnit y GitHub Actions | `feat/phpunit-ci-alexandro` | #20 |
| MVC | `refactor/mvc-integracion` | #21 |

Todos estos Pull Requests fueron fusionados en `integracion/entrega-tp`.

La rama `main` no fue utilizada como destino directo de estas integraciones.

### Revisión de código

Las revisiones de los Pull Requests permitieron comprobar:

- Correspondencia entre la deuda identificada y la solución aplicada.
- Uso adecuado del patrón de diseño seleccionado.
- Separación de responsabilidades.
- Compatibilidad con los patrones previamente integrados.
- Conservación de las funcionalidades existentes.
- Incorporación de pruebas automatizadas.
- Ausencia de conflictos de integración.
- Resultados satisfactorios de GitHub Actions.

Cuando se detectaron incompatibilidades, se solicitaron correcciones antes de aprobar la integración.

### Resolución de conflictos

Durante la integración se presentaron conflictos derivados de modificaciones realizadas sobre archivos compartidos.

Uno de los casos relevantes ocurrió en:

`src/Services/OrderService.php`

Este archivo había sido modificado durante la implementación de Repository, Factory y Decorator, mientras que Facade requería reorganizar su estructura.

La resolución consistió en conservar la implementación de Facade y verificar que las responsabilidades existentes quedaran delegadas correctamente a sus respectivos componentes.

También fue necesario adaptar pruebas para mantener la compatibilidad con los nuevos contratos de los servicios.

### Integración continua

Se incorporó GitHub Actions para ejecutar automáticamente la suite de PHPUnit durante el proceso de integración.

El workflow se encuentra en:

`.github/workflows/ci.yml`

La ejecución automática permitió detectar posibles regresiones y verificar que los cambios individuales no afectaran el funcionamiento general del sistema.

### Resultado de la integración final

Después de fusionar las implementaciones de los patrones y MVC, se ejecutaron las pruebas tanto en GitHub Actions como en el entorno local de Windows.

**Resultado final verificado:**

- 119 pruebas PHPUnit aprobadas.
- 223 assertions exitosas.
- GitHub Actions aprobado.
- Ejecución local con PHP 8.4.23.
- PHPUnit 11.5.58.
- Sin fallos en la suite automatizada.

También se realizaron pruebas funcionales sobre las rutas principales:

| Prueba funcional | Resultado |
|---|---|
| Crear pedido | Correcto |
| Listar pedidos | Correcto |
| Generar reporte | Correcto |
| Paciente inválido | HTTP 400 |
| Acción inexistente | HTTP 404 |

Estas verificaciones proporcionan evidencia del funcionamiento de la aplicación integrada.

---

## Criterios de refactorización y calidad

Durante el desarrollo se aplicaron criterios para mejorar la mantenibilidad, la extensibilidad y la capacidad de prueba del código.

### Principios aplicados

**Responsabilidad única (SRP)**

Cada componente debe tener una responsabilidad claramente definida. La persistencia, el cálculo de precios, las notificaciones y la presentación se encuentran separados.

**Abierto/Cerrado (OCP)**

Las implementaciones deben permitir incorporar nuevos comportamientos sin modificar innecesariamente las clases existentes. Strategy, Factory y Decorator contribuyen a este objetivo.

**Inversión de dependencias (DIP)**

Se procura reducir el acoplamiento mediante contratos, abstracciones e inyección de dependencias. Facade coordina servicios especializados en lugar de implementar todas las operaciones.

**Separación de capas mediante MVC**

El controlador recibe solicitudes y selecciona las vistas. Las reglas de negocio se delegan en los servicios correspondientes y las vistas presentan los datos preparados.

### Reglas adoptadas durante la refactorización

- Conservar las funcionalidades originales dentro del alcance académico.
- Evitar la duplicación de reglas de negocio.
- No implementar nuevamente patrones que ya existen.
- Mantener la compatibilidad entre componentes.
- Utilizar interfaces y abstracciones cuando aporten flexibilidad.
- Separar la lógica de negocio de la presentación.
- Escapar los datos dinámicos mostrados en HTML.
- Incorporar pruebas que permitan verificar los cambios.
- Documentar las decisiones técnicas y sus consecuencias negativas.
- Revisar los cambios antes de incorporarlos a la rama de integración.

### Evidencias del trabajo

El historial del repositorio permite consultar:

- Ramas utilizadas durante el desarrollo.
- Commits realizados.
- Pull Requests de cada implementación.
- Revisiones y correcciones.
- Conflictos de integración resueltos.
- Resultados de GitHub Actions.
- Pruebas automatizadas incorporadas.

Estas evidencias respaldan el proceso colaborativo y las decisiones adoptadas durante la refactorización.

### Consideraciones finales

La aplicación de patrones de diseño permitió mejorar la organización del proyecto y reducir diferentes formas de deuda técnica.

La utilización de Git, GitHub, PHPUnit y GitHub Actions facilitó la integración progresiva y la verificación de los cambios.

El resultado es una aplicación académica con responsabilidades mejor delimitadas, componentes reutilizables y pruebas automatizadas.

El proyecto conserva algunas limitaciones propias de su alcance educativo, especialmente la persistencia simulada y las notificaciones de demostración.

Estas limitaciones deben considerarse antes de utilizar la aplicación en un entorno productivo.