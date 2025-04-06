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

    // // Listar api-keys
    // $apiResponse = $client->getApiKeyService()->list();
    // consoleLog($apiResponse);  

    // // Obtener api-key por id
    // $apiResponse = $client->getApiKeyService()->get('d4d21a4d-2e58-4b98-a33e-156524e24bce');
    // consoleLog($apiResponse);   

    // // Crear api-key
    // $data = [
    //     'personId' => '4162d2e2-d63b-4923-85eb-db1ed4e700c1', // id de la persona (usuario) a quien se le asigna la api key
    //     'description' => 'Api key del cliente abc123', // descripcion de la api key
    // ];
    // $apiResponse = $client->getApiKeyService()->create($data);
    // consoleLog($apiResponse);
    

    // // Actualizar api-key por id
    // $data = [
    //     'id' => '292821b4-7ddd-42a2-8ea0-6dfa546fad82', // id de la api key a actualizar
    //     'description' => 'Api key del cliente abc123 actualizada', // descripcion de la api key
    //     "apiKeyStatus" => 0 // 0: INACTIVE, 1: ACTIVE
    // ];
    // $apiResponse = $client->getApiKeyService()->update($data);
    // consoleLog($apiResponse);


    // // Eliminar api-key por id
    // $apiResponse = $client->getApiKeyService()->delete('292821b4-7ddd-42a2-8ea0-6dfa546fad82');
    // consoleLog($apiResponse);

    
    // // Listar personas
    // $apiResponse = $client->getPersonService()->list(1,2); // 1: pagina 1, 2: 2 items por pagina
    // consoleLog($apiResponse); 

    // Obtener persona (usuario/emisor/receptor/cliente, etc ) por id 
    // $apiResponse = $client->getPersonService()->get('3f3478b4-60fd-459e-8bfc-f8239fc96257');
    // consoleLog($apiResponse); 

    // // Crear persona (usuario/emisor/receptor/cliente, etc ) con datos minimos requeridos
    // $data = [
    //     'legalName' => 'MI EMPRESA',
    //     'capitalRegime' => "S.A. DE C.V.", // omitir cuando sea persona fisica
    //     'email' => 'juan.perez@example.com',
    //     'password' => 'YourPassword123!', // contraseña el dashboard de fiscalapi. usted decide si su cliente tiene acceso al dashboard o no, entregando las contraseña o no.
    // ];
    // $apiResponse = $client->getPersonService()->create($data);
    // consoleLog($apiResponse);


    // Actualizar persona (usuario/emisor/receptor/cliente, etc ) por id
    // $data = [
    //     'id' => '39b32f44-b975-4f3b-a3f9-cb093eb0baa9', // id de la persona a actualizar
    //     'legalName' => 'MI EMPRESA ACTUALIZADA',
    //     'capitalRegime' => "S DE R.L. DE C.V.", // omitir cuando sea persona fisica
    //     //'email' => 'juan.perez@example.com', // solo si necesita actualizar el email
    //     'password' => 'YourPassword123!', // contraseña el dashboard de fiscalapi. usted decide si su cliente tiene acceso al dashboard o no, entregando las contraseña o no.
    //     'phoneNumber' => '+521234567890', // solo si necesita actualizar el telefono
    //     'satTaxRegimeId' => '601', //Regimen fiscal General de Ley Personas Morales
    //     'satCfdiUseId' => 'G03', //Uso de CFDI G03	Gastos en general cuando el contexto de la persona es receptor 
    //     'userTypeId' => 'T', // T: Tenant, C: Cliente , U: Usuario (solo con fines informativos)
    //     'tin' => 'XEXX010101AAA', //RFC  debe coincidir con el RFC de la constancia de situacion fiscal
    //     'zipCode' => '12345', //Codigo postal debe coincidir con el codigo postal de la constancia de situacion fiscal
    //     'base64Photo' => null, //Foto en base64 paara el dashboard de fiscalapi
    //     'taxPassword' => '12345678a', //Contraseña de los sellos CSD cuando el contexto de la persona es emisor 
    // ];
    // $apiResponse = $client->getPersonService()->update($data);
    // consoleLog($apiResponse);


    // // Eliminar persona (usuario/emisor/receptor/cliente, etc ) por id
    // $apiResponse = $client->getPersonService()->delete('39b32f44-b975-4f3b-a3f9-cb093eb0baa9');
    // consoleLog($apiResponse);


    // // Listar certificados
    $apiResponse = $client->getTaxFileService()->list(1,2); 
    consoleLog($apiResponse);    








    ////Listar productos
    //$apiResponse = $client->getProductService()->list(1,2);
    //consoleLog($apiResponse);

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