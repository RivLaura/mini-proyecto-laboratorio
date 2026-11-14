<?php
/**
 * ============================================================================
 *  GENERACION DE REPORTES
 *  Patron aplicado: DECORATOR (estructural)
 * ============================================================================
 *
 *  DEUDA CORREGIDA
 *     Antes, ReportGenerator::generate() recibia banderas booleanas
 *     (boolean trap): generate('x', true, true, false). Con 3 banderas habia
 *     8 combinaciones dentro del mismo metodo y cada agregado nuevo sumaba
 *     un parametro y un if (violaba OCP).
 *
 *  SOLUCION
 *     Report es el contrato comun. BasicReport es el reporte base y cada
 *     agregado es un decorador que envuelve otro Report:
 *
 *       $reporte = new BasicReport('Pedido 1');
 *       $reporte = new DigitalSignatureDecorator($reporte);
 *       $reporte = new PdfReportDecorator($reporte);
 *       echo $reporte->generate();  // Reporte: Pedido 1 + firma digital + PDF
 *
 *     Agregar "codigo QR" es AGREGAR una clase, no modificar las existentes.
 *     Los agregados son descripciones de texto (" + PDF"): no se genera un
 *     PDF real, igual que en el codigo original.
 *
 *  CONSECUENCIA NEGATIVA
 *     El orden de los decoradores cambia el resultado: cada uno agrega su
 *     texto al final del reporte que envuelve. Firmar y despues pasar a PDF
 *     no es lo mismo que al reves. Orden acordado para el equipo (el mismo
 *     que tenian las banderas originales):
 *
 *       BasicReport -> DigitalSignature -> Pdf -> Watermark
 *
 *     Ademas, la cantidad de clases crece (una por agregado) y para armar
 *     un reporte hay que encadenar varios new.
 * ============================================================================
 */
interface Report
{
    public function generate(): string;
}
