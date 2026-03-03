<?php
declare(strict_types=1);

/**
 * Ejemplos de Impuestos Locales por Referencias
 *
 * Este archivo contiene ejemplos de cómo crear facturas de ingreso con complemento
 * de impuestos locales, usando referencias (IDs de personas e items existentes).
 * Incluye 3 variantes: CEDULAR + ISH, solo CEDULAR, solo ISH.
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

require_once __DIR__ . '/../vendor/autoload.php';


// Crea las configuraciones del cliente http fiscalapi.
$settings = new FiscalApiSettings(
    'https://test.fiscalapi.com',
    '<apiKey>',
    '<tenant>',
    false,
    false,
);

// IDs de personas previamente registradas en FiscalAPI
$escuelaKemperUrgateId = "0e82a655-5f0c-4e07-abab-8f322e4123ef";
$karlaFuenteNolascoId = "da71df0c-f328-45ee-9bd9-3096ed02c164";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  FACTURA DE INGRESO CON IMPUESTOS LOCALES (CEDULAR + ISH) POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto CEDULAR + ISH por referencias
    // ------------------------------------------------------------------
    // $invoiceWithLocalTaxes = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'paymentFormCode' => "01",
    //     'paymentConditions' => "Contado",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'paymentMethodCode' => "PUE",
    //     'exchangeRate' => 1.0,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $karlaFuenteNolascoId
    //     ],
    //     'recipient' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'items' => [
    //         ['id' => "2f3c65f3-ed02-452f-944c-97a47010df5c"],
    //         ['id' => "037b5705-a9a2-4422-b842-97c0f9347498"],
    //         ['id' => "40389443-2aa1-4121-b86a-e8c45fac6b17"]
    //     ],
    //     // Complemento de impuestos locales
    //     'complement' => [
    //         'localTaxes' => [
    //             'taxes' => [
    //                 [
    //                     'taxName' => "CEDULAR",
    //                     'taxRate' => 3.00,
    //                     'taxAmount' => 6.00,
    //                     'taxFlagCode' => "R"
    //                 ],
    //                 [
    //                     'taxName' => "ISH",
    //                     'taxRate' => 8.00,
    //                     'taxAmount' => 16.00,
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoiceWithLocalTaxes);
    // consoleLog($apiResponse);


    // ==================================================================
    //  FACTURA DE INGRESO CON IMPUESTO CEDULAR (SOLO) POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto CEDULAR por referencias
    // ------------------------------------------------------------------
    // $invoiceWithCedular = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'paymentFormCode' => "01",
    //     'paymentConditions' => "Contado",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'paymentMethodCode' => "PUE",
    //     'exchangeRate' => 1.0,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $karlaFuenteNolascoId
    //     ],
    //     'recipient' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'items' => [
    //         ['id' => "2f3c65f3-ed02-452f-944c-97a47010df5c"],
    //         ['id' => "037b5705-a9a2-4422-b842-97c0f9347498"],
    //         ['id' => "40389443-2aa1-4121-b86a-e8c45fac6b17"]
    //     ],
    //     // Complemento de impuestos locales (solo CEDULAR)
    //     'complement' => [
    //         'localTaxes' => [
    //             'taxes' => [
    //                 [
    //                     'taxName' => "CEDULAR",
    //                     'taxRate' => 3.00,
    //                     'taxAmount' => 6.00,
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoiceWithCedular);
    // consoleLog($apiResponse);


    // ==================================================================
    //  FACTURA DE INGRESO CON IMPUESTO ISH (SOLO) POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto ISH por referencias
    // ------------------------------------------------------------------
    // $invoiceWithIsh = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'paymentFormCode' => "01",
    //     'paymentConditions' => "Contado",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'paymentMethodCode' => "PUE",
    //     'exchangeRate' => 1.0,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $karlaFuenteNolascoId
    //     ],
    //     'recipient' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'items' => [
    //         ['id' => "2f3c65f3-ed02-452f-944c-97a47010df5c"],
    //         ['id' => "037b5705-a9a2-4422-b842-97c0f9347498"],
    //         ['id' => "40389443-2aa1-4121-b86a-e8c45fac6b17"]
    //     ],
    //     // Complemento de impuestos locales (solo ISH)
    //     'complement' => [
    //         'localTaxes' => [
    //             'taxes' => [
    //                 [
    //                     'taxName' => "ISH",
    //                     'taxRate' => 8.00,
    //                     'taxAmount' => 16.00,
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoiceWithIsh);
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

/**
 * Obtiene la fecha y hora actual en la zona horaria de México Central
 */
function getCurrentDate(): string {
    date_default_timezone_set('Etc/GMT+6');
    return date('Y-m-d\TH:i:s');
}
