<?php
declare(strict_types=1);

/**
 * Ejemplos de Timbres (Stamp Transactions)
 *
 * Este archivo contiene ejemplos de cómo usar el servicio de timbres de FiscalAPI.
 * Incluye operaciones de listar, obtener por ID, transferir y retirar timbres.
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
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

// Crear cliente HTTP
$client = new FiscalApiClient($settings);

try {

    // ------------------------------------------------------------------
    // Listar movimientos de timbres: pageNumber=1, pageSize=10
    // ------------------------------------------------------------------
    // $apiResponse = $client->getStampService()->list(1, 10);
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Obtener movimiento de timbres por ID
    // ------------------------------------------------------------------
    // $apiResponse = $client->getStampService()->get("e29720d5-fa29-4690-bdb3-9fc8344fcef8");
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Transferir timbres
    // ------------------------------------------------------------------
    // $transParams = [
    //     'fromPersonId' => "0e82a655-5f0c-4e07-abab-8f322e4123ef",
    //     'toPersonId' => "da71df0c-f328-45ee-9bd9-3096ed02c164",
    //     'amount' => 1,
    //     'comments' => "venta de timbres",
    // ];
    // $apiResponse = $client->getStampService()->transferStamps($transParams);
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Retirar timbres
    // ------------------------------------------------------------------
    // $transParams = [
    //     'fromPersonId' => "da71df0c-f328-45ee-9bd9-3096ed02c164",
    //     'toPersonId' => "0e82a655-5f0c-4e07-abab-8f322e4123ef",
    //     'amount' => 1,
    //     'comments' => "prestamo",
    // ];
    // $apiResponse = $client->getStampService()->withdrawStamps($transParams);
    // consoleLog($apiResponse);


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
