<?php
declare(strict_types=1);

/**
 * Ejemplos de Tickets de Auto CFDI
 *
 * Este archivo contiene ejemplos de cómo usar el servicio de tickets de FiscalAPI: ingerir una venta pagada del punto
 * de venta (cobra 1 crédito de ticket), listar los tickets pendientes de un emisor en un periodo (para la factura
 * global), consultar el detalle, reimprimir el código y cancelar un ticket.
 *
 * Con Idempotency-Key, repetir la misma key con el mismo cuerpo responde 200 con el ticket original sin cobrar otra
 * vez. Un folio y serie ya activos responden 409 ('data.existingTicketId'); un total que no cuadra al centavo, 422
 * ('data.expectedTotal' y 'data.computedTotal').
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
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

// Id de la persona emisora (con CSD y créditos de ticket)
$issuerId = '<personId>';

// Crear cliente HTTP
$client = new FiscalApiClient($settings);
$ticketService = $client->getTicketService();

try {
    // 1. Ingerir un ticket: un refresco de 100 + IVA 16 % = 116, pagado en efectivo.
    // paymentDate es hora civil de México sin zona; los decimales en texto conservan sus ceros finales.
    $ticket = [
        'issuer' => ['id' => $issuerId],
        'folio' => 'F-1001',
        'series' => 'A',
        'paymentDate' => '2026-10-15T12:30:00',
        'paymentFormCode' => '01',
        'total' => '116.00',
        'items' => [[
            'itemCode' => '50202306',
            'itemSku' => 'SKU-1',
            'quantity' => 1,
            'unitOfMeasurementCode' => 'H87',
            'description' => 'Refresco',
            'unitPrice' => '100.00',
            'taxObjectCode' => '02',
            'itemTaxes' => [['taxCode' => '002', 'taxTypeCode' => 'Tasa', 'taxRate' => '0.160000', 'taxFlagCode' => 'T']],
        ]],
    ];

    $created = $ticketService->create($ticket, 'pos-01-F-1001');
    consoleLog($created);

    $ticketId = null;
    if ($created->isSuccessful()) {
        // El código y la URL del QR se imprimen en el ticket de venta.
        $ticketId = $created->getJson()['data']['id'];
    } elseif ($created->getStatusCode() === 409) {
        echo "El folio ya está activo en el ticket " . $created->getJson()['data']['existingTicketId'] . "\n";
    }

    // 2. Tickets pendientes del emisor en octubre (fechas civiles de México, ambas incluidas).
    $pending = $ticketService->getList([
        'status' => [TicketStatus::PENDING],
        'personId' => $issuerId,
        'from' => '2026-10-01',
        'to' => '2026-10-31',
        'pageNumber' => 1,
        'pageSize' => 50,
    ]);
    consoleLog($pending);

    if ($ticketId !== null) {
        // 3. Detalle con conceptos e historia, código para reimprimir y cancelación (solo en estado pending;
        // el crédito de ticket no se devuelve).
        consoleLog($ticketService->getById($ticketId, true));
        consoleLog($ticketService->getCode($ticketId));
        consoleLog($ticketService->cancel($ticketId));
    }
} catch (\Exception $e) {
    consoleError($e);
}

function consoleLog(FiscalApiHttpResponseInterface $apiResponse) {
    echo "apiResponse:\n" . json_encode($apiResponse->getJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

function consoleError(\Exception $e) {
    echo "Error en la ejecución: " . $e->getMessage() . "\n";
    echo "Traza: " . $e->getTraceAsString() . "\n";
}
