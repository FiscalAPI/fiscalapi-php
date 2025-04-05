<?php
declare(strict_types=1);

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

require_once 'vendor/autoload.php';


// Crear configuración
$settings = new FiscalApiSettings(
    'https://localhost:7173',
    'sk_development_833a58f9_8212_43ce_b544_f2fa93b1e895',
    'e839651d-1765-4cd0-ba7f-547a4c20580f',
    true, // Imprimir raw request / response
    false, // Desactivar verificación SSL (recomendado solo en desarrollo)
);

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    //Listar productos
    $apiResponse = $client->getProductService()->list(1,2);
    consoleLog($apiResponse);

} catch (\Exception $e) {
    consoleError($e);
}

function consoleLog(FiscalApiHttpResponseInterface $apiResponse) {
    echo "apiResponse:\n" . json_encode($apiResponse->getJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "\n";
}
function consoleError(Exception $e) {
    echo "Error en la ejecución: " . $e->getMessage() . "\n";
    echo "Traza: " . $e->getTraceAsString() . "\n";
}