<?php
declare(strict_types=1);

/**
 * Ejemplos de Validaciones SAT
 *
 * Este archivo contiene ejemplos de cómo usar el servicio de validaciones SAT de FiscalAPI.
 * Incluye la consulta del catálogo de tipos, los estatus que cada tipo puede tomar y la
 * ejecución de validaciones sobre un CFDI timbrado o sobre un RFC.
 *
 * Cada tipo de validación solicitado consume un crédito de validación. El cobro es todo o nada
 * y ocurre antes de ejecutar: si el saldo no alcanza para todos los tipos solicitados no se
 * ejecuta ninguno y la API responde 403.
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Models\SatValidationTypeIds;
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

// Ruta al CFDI timbrado que se desea validar. Ajústela a la ubicación de su comprobante.
$xmlPath = 'C:\facturas\FacturaXml.xml';

// El campo xml viaja en base64
$xmlBase64 = is_file($xmlPath) ? base64_encode((string) file_get_contents($xmlPath)) : '';

// RFC a consultar en las listas negras cuando no se envía el CFDI completo
$tin = "XAXX010101000";

// Crear cliente HTTP
$client = new FiscalApiClient($settings);

try {

    // ==================================================================
    //  CATÁLOGO DE VALIDACIONES
    // ==================================================================

    // ------------------------------------------------------------------
    // Listar los tipos de validación disponibles
    // ------------------------------------------------------------------
    // $apiResponse = $client->getSatValidationService()->getTypes();
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Obtener un tipo de validación por su id
    // ------------------------------------------------------------------
    // $apiResponse = $client->getSatValidationService()->getTypeById(SatValidationTypeIds::CFDI_STATUS);
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Obtener los estatus que un tipo de validación puede tomar.
    // Es una consulta de catálogo: no ejecuta la validación ni consume créditos.
    // ------------------------------------------------------------------
    // $apiResponse = $client->getSatValidationService()->getStatuses(SatValidationTypeIds::CFDI_STATUS);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJECUTAR VALIDACIONES SOBRE UN CFDI (CONSUME CRÉDITOS)
    // ==================================================================

    // ------------------------------------------------------------------
    // Validar la estructura del XML y la situación del emisor en el listado 69-B.
    // Consume 2 créditos de validación.
    // ------------------------------------------------------------------
    // $validationRequest = [
    //     'xml' => $xmlBase64,
    //     'validationTypes' => [
    //         SatValidationTypeIds::XML_STRUCTURE,
    //         SatValidationTypeIds::BLACKLIST_69B,
    //     ],
    // ];
    // $apiResponse = $client->getSatValidationService()->validate($validationRequest);
    // consoleLog($apiResponse);


    // ------------------------------------------------------------------
    // Validar el CFDI con todos los tipos disponibles.
    // Consume 7 créditos de validación.
    // ------------------------------------------------------------------
    // $validationRequest = [
    //     'xml' => $xmlBase64,
    //     'validationTypes' => [
    //         SatValidationTypeIds::XML_STRUCTURE,
    //         SatValidationTypeIds::CERTIFICATE_VALIDITY,
    //         SatValidationTypeIds::CFDI_SELLO,
    //         SatValidationTypeIds::TFD_SELLO,
    //         SatValidationTypeIds::CFDI_STATUS,
    //         SatValidationTypeIds::BLACKLIST_69B,
    //         SatValidationTypeIds::BLACKLIST_69B_BIS,
    //     ],
    // ];
    // $apiResponse = $client->getSatValidationService()->validate($validationRequest);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJECUTAR VALIDACIONES SOBRE UN RFC (CONSUME CRÉDITOS)
    // ==================================================================

    // ------------------------------------------------------------------
    // Sin el CFDI solo pueden solicitarse las listas negras, y el RFC consultado es el del campo tin.
    // Consume 2 créditos de validación.
    // ------------------------------------------------------------------
    // $validationRequest = [
    //     'tin' => $tin,
    //     'validationTypes' => [
    //         SatValidationTypeIds::BLACKLIST_69B,
    //         SatValidationTypeIds::BLACKLIST_69B_BIS,
    //     ],
    // ];
    // $apiResponse = $client->getSatValidationService()->validate($validationRequest);
    // consoleLog($apiResponse);


    // ==================================================================
    //  INTERPRETAR LOS RESULTADOS
    // ==================================================================

    // ------------------------------------------------------------------
    // Los resultados llegan en el orden del catálogo, no en el solicitado.
    // El veredicto de cada validación es la clave passed; un resultado adverso
    // sigue siendo una respuesta exitosa.
    // ------------------------------------------------------------------
    // $validationRequest = [
    //     'xml' => $xmlBase64,
    //     'validationTypes' => [
    //         SatValidationTypeIds::CFDI_SELLO,
    //         SatValidationTypeIds::CFDI_STATUS,
    //     ],
    // ];
    // $apiResponse = $client->getSatValidationService()->validate($validationRequest);
    //
    // $results = $apiResponse->getJson()['data'] ?? [];
    //
    // foreach ($results as $result) {
    //     printf(
    //         "%-26s %-20s %s\n",
    //         $result['type']['id'],
    //         $result['status']['id'],
    //         $result['passed'] ? 'APROBADA' : 'NO APROBADA'
    //     );
    //
    //     // details es texto libre con los hechos del caso y puede ser null
    //     if ($result['status']['details'] !== null) {
    //         echo "    " . $result['status']['details'] . "\n";
    //     }
    // }


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
