<?php
declare(strict_types=1);

/**
 * Ejemplo de factura global de tickets de Auto CFDI (POST /api/v4/invoices)
 *
 * La global de tickets es una factura de ingreso estándar (getInvoiceService()->create()) con 'globalInformation', el
 * emisor por referencia ('issuer.id') y 'ticketId' en cada concepto. Cada ticket va en un concepto por grupo de objeto
 * de impuesto e impuestos (un ticket con IVA 16 % y 0 % lleva dos conceptos con el mismo 'ticketId'), y la suma de sus
 * conceptos debe reflejar el ticket (si no, 400 en items[i].ticketId). Si algún ticket ya no está pendiente, 409 con
 * 'data.unavailableTickets': no se cobra el timbre ni queda tomado ningún ticket.
 *
 * Una global lleva una sola moneda, tipo de cambio y lugar de expedición, y hasta 1,500 conceptos: este ejemplo asume
 * tickets en MXN de un solo lugar de expedición. Una global sin 'ticketId' no marca los tickets.
 */

use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Models\TicketStatus;
use Fiscalapi\Services\FiscalApiClient;

require_once __DIR__ . '/../vendor/autoload.php';


// Crea las configuraciones del cliente http fiscalapi.
// Lea como obtener sus credenciales: https://docs.fiscalapi.com/credentials-info

// Ambiente de pruebas
$settings = new FiscalApiSettings(
    'https://test.fiscalapi.com',
    '<apiKey>',
    '<tenant>',
    false, // Imprimir raw request / response
    false, // Desactivar verificación SSL
);

// Id de la persona emisora (con CSD)
$issuerId = '<personId>';

// Crear cliente HTTP
$client = new FiscalApiClient($settings);
$ticketService = $client->getTicketService();

try {
    // 1. Tickets pendientes del emisor en septiembre, con sus conceptos (detalle de cada uno).
    $tickets = [];
    for ($pageNumber = 1; ; $pageNumber++) {
        $page = $ticketService->getList([
            'status' => [TicketStatus::PENDING],
            'personId' => $issuerId,
            'from' => '2026-09-01',
            'to' => '2026-09-30',
            'pageNumber' => $pageNumber,
            'pageSize' => 50,
        ])->getJson()['data'];
        foreach ($page['items'] as $summary) {
            $tickets[] = $ticketService->getById($summary['id'], true)->getJson()['data'];
        }
        if (!$page['hasNextPage']) {
            break;
        }
    }

    if ($tickets === []) {
        echo "No hay tickets pendientes en el periodo.\n";
        return;
    }

    // 2. Un concepto por ticket y grupo de impuestos, y la factura global a público en general.
    $items = [];
    foreach ($tickets as $ticket) {
        foreach (ticketConcepts($ticket) as $concept) {
            $items[] = $concept;
        }
    }

    $expeditionZipCode = $tickets[0]['expeditionZipCode'];
    $globalInvoice = [
        'versionCode' => '4.0',
        'series' => 'G',
        'date' => date('Y-m-d\TH:i:s'),
        'paymentFormCode' => largestTicket($tickets)['paymentFormCode'], // la del ticket de mayor monto
        'paymentMethodCode' => 'PUE',
        'currencyCode' => 'MXN',
        'typeCode' => 'I',
        'expeditionZipCode' => $expeditionZipCode,
        'exportCode' => '01',
        'issuer' => ['id' => $issuerId],
        'recipient' => [
            'tin' => 'XAXX010101000',
            'legalName' => 'PUBLICO EN GENERAL',
            'zipCode' => $expeditionZipCode,
            'taxRegimeCode' => '616',
            'cfdiUseCode' => 'S01',
        ],
        'globalInformation' => ['periodicityCode' => '04', 'monthCode' => '09', 'year' => 2026],
        'items' => $items,
    ];

    // 3. Timbrar la global.
    $response = $client->getInvoiceService()->create($globalInvoice);
    if ($response->isSuccessful()) {
        echo "Global timbrada: " . $response->getJson()['data']['uuid'] . "\n";
    } elseif ($response->getStatusCode() === 409) {
        // Algún ticket ya no estaba pendiente: no se cobró el timbre ni quedó tomado ningún ticket.
        foreach ($response->getJson()['data']['unavailableTickets'] as $unavailable) {
            echo "- {$unavailable['ticketId']}: {$unavailable['status']}\n";
        }
    } else {
        // 400 en items[i].ticketId: los conceptos de ese ticket no lo reflejan (importes, descuentos o impuestos).
        echo "Error {$response->getStatusCode()}: " . $response->getJson()['details'] . "\n";
    }
} catch (\Exception $e) {
    echo "Error en la ejecución: " . $e->getMessage() . "\n";
}

/**
 * Los conceptos de la global de un ticket: uno por grupo de objeto de impuesto e impuestos, con valor unitario = suma de
 * cantidad × valor unitario (6 decimales por concepto) y descuento = suma de descuentos. Se suma en millonésimas
 * enteras para no acumular errores de punto flotante; los importes viajan como texto con 6 decimales.
 */
function ticketConcepts(array $ticket): array
{
    $groups = [];
    foreach ($ticket['items'] as $item) {
        $groups[taxSignature($item)][] = $item;
    }

    $concepts = [];
    foreach ($groups as $items) {
        $amount = 0;
        $discount = 0;
        foreach ($items as $item) {
            $amount += (int) round((float) $item['quantity'] * (float) $item['unitPrice'] * 1000000);
            $discount += (int) round((float) ($item['discount'] ?? 0) * 1000000);
        }

        $concepts[] = [
            'ticketId' => $ticket['id'],
            'itemCode' => '01010101',
            'itemSku' => $ticket['folio'], // NoIdentificacion: folio del ticket
            'quantity' => 1,
            'unitOfMeasurementCode' => 'ACT',
            'description' => 'Venta',
            'unitPrice' => sprintf('%.6f', $amount / 1000000),
            'discount' => sprintf('%.6f', $discount / 1000000),
            'taxObjectCode' => $items[0]['taxObjectCode'],
            'itemTaxes' => array_map(static function (array $tax): array {
                return [
                    'taxCode' => $tax['taxCode'],
                    'taxTypeCode' => $tax['taxTypeCode'],
                    'taxRate' => sprintf('%.6f', (float) $tax['taxRate']), // 6 decimales (CFDI40179)
                    'taxFlagCode' => $tax['taxFlagCode'],
                ];
            }, $items[0]['itemTaxes'] ?? []),
        ];
    }

    return $concepts;
}

/** Objeto de impuesto e impuestos de un concepto, sin importar el orden de los impuestos. */
function taxSignature(array $item): string
{
    $taxes = array_map(static function (array $tax): string {
        return implode('|', [$tax['taxCode'], $tax['taxTypeCode'], sprintf('%.6f', (float) $tax['taxRate']), $tax['taxFlagCode']]);
    }, $item['itemTaxes'] ?? []);
    sort($taxes, SORT_STRING);

    return implode(';', array_merge([$item['taxObjectCode']], $taxes));
}

/** El ticket de mayor monto (con empate, el primero). */
function largestTicket(array $tickets): array
{
    $largest = $tickets[0];
    foreach ($tickets as $ticket) {
        if ((float) $ticket['total'] > (float) $largest['total']) {
            $largest = $ticket;
        }
    }

    return $largest;
}
