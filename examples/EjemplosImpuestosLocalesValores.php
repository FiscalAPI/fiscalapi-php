<?php
declare(strict_types=1);

/**
 * Ejemplos de Impuestos Locales por Valores
 *
 * Este archivo contiene ejemplos de cómo crear facturas de ingreso con complemento
 * de impuestos locales, usando datos completos (por valores).
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

// Sellos SAT de prueba (KARLA FUENTE NOLASCO FUNK671228PH6)
$base64Cert = "MIIFgDCCA2igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0NDYwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTQzNTM3WhcNMjcwNTE4MTQzNTM3WjCBpzEdMBsGA1UEAxMUS0FSTEEgRlVFTlRFIE5PTEFTQ08xHTAbBgNVBCkTFEtBUkxBIEZVRU5URSBOT0xBU0NPMR0wGwYDVQQKExRLQVJMQSBGVUVOVEUgTk9MQVNDTzEWMBQGA1UELRMNRlVOSzY3MTIyOFBINjEbMBkGA1UEBRMSRlVOSzY3MTIyOE1DTE5MUjA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhNXbTSqGX6+/3Urpemyy5vVG2IdP2v7v001+c4BoMxEDFDQ32cOFdDiRxy0Fq9aR+Ojrofq8VeftvN586iyA1A6a0QnA68i7JnQKI4uJy+u0qiixuHu6u3b3BhSpoaVHcUtqFWLLlzr0yBxfVLOqVna/1/tHbQJg9hx57mp97P0JmXO1WeIqi+Zqob/mVZh2lsPGdJ8iqgjYFaFn9QVOQ1Pq74o1PTqwfzqgJSfV0zOOlESDPWggaDAYE4VNyTBisOUjlNd0x7ppcTxSi3yenrJHqkq/pqJsRLKf6VJ/s9p6bsd2bj07hSDpjlDC2lB25eEfkEkeMkXoE7ErXQ5QCwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAHwYpgbClHULXYhK4GNTgonvXh81oqfXwCSWAyDPiTYFDWVfWM9C4ApxMLyc0XvJte75Rla+bPC08oYN3OlhbbvP3twBL/w9SsfxvkbpFn2ZfGSTXZhyiq4vjmQHW1pnFvGelwgU4v3eeRE/MjoCnE7M/Q5thpuog6WGf7CbKERnWZn8QsUaJsZSEkg6Bv2jm69ye57ab5rrOUaeMlstTfdlaHAEkUgLX/NXq7RbGwv82hkHY5b2vYcXeh34tUMBL6os3OdRlooN9ZQGkVIISvxVZpSHkYC20DFNh1Bb0ovjfujlTcka81GnbUhFGZtRuoVQ1RVpMO8xtx3YKBLp4do3hPmnRCV5hCm43OIjYx9Ov2dqICV3AaNXSLV1dW39Bak/RBiIDGHzOIW2+VMPjvvypBjmPv/tmbqNHWPSAWOxTyMx6E1gFCZvi+5F+BgkdC3Lm7U0BU0NfvsXajZd8sXnIllvEMrikCLoI/yurvexNDcF1RW/FhMsoua0eerwczcNm66pGjHm05p9DR6lFeJZrtqeqZuojdxBWy4vH6ghyJaupergoX+nmdG3JYeRttCFF/ITI68TeCES5V3Y0C3psYAg1XxcGRLGd4chPo/4xwiLkijWtgt0/to5ljGBwfK7r62PHZfL1Dp+i7V3w7hmOlhbXzP+zhMZn1GCk7KY=";
$base64Key = "MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIAgEAAoIBAQACAggAMBQGCCqGSIb3DQMHBAgwggS9AgEAMASCBMh4EHl7aNSCaMDA1VlRoXCZ5UUmqErAbucRBAKNQXH8t8gVCl/ItHMI2hMJ76QOECOqEi1Y89cDpegDvh/INXyMsXbzi87tfFzgq1O+9ID6aPWGg+bNGADXyXxDVdy7Nq/SCdoXvo66MTYwq8jyJeUHDHEGMVBcmZpD44VJCvLBxDcvByuevP4Wo2NKqJCwK+ecAdZc/8Rvd947SjbMHuS8BppfQWARVUqA5BLOkTAHNv6tEk/hncC7O2YOGSShart8fM8dokgGSyewHVFe08POuQ+WDHeVpvApH/SP29rwktSoiHRoL6dK+F2YeEB5SuFW9LQgYCutjapmUP/9TC3Byro9Li6UrvQHxNmgMFGQJSYjFdqlGjLibfuguLp7pueutbROoZaSxU8HqlfYxLkpJUxUwNI1ja/1t3wcivtWknVXBd13R06iVfU1HGe8Kb4u5il4a4yP4p7VT4RE3b1SBLJeG+BxHiE8gFaaKcX/Cl6JV14RPTvk/6VnAtEQ66qHJex21KKuiJo2JoOmDXVHmvGQlWXNjYgoPx28Xd5WsofL+n7HDR2Ku8XgwJw6IXBJGuoday9qWN9v/k7DGlNGB6Sm4gdVUmycMP6EGhB1vFTiDfOGQO42ywmcpKoMETPVQ5InYKE0xAOckgcminDgxWjtUHjBDPEKifEjYudPwKmR6Cf4ZdGvUWwY/zq9pPAC9bu423KeBCnSL8AQ4r5SVsW6XG0njamwfNjpegwh/YG7sS7sDtZ8gi7r6tZYjsOqZlCYU0j7QTBpuQn81Yof2nQRCFxhRJCeydmIA8+z0nXrcElk7NDPk4kYQS0VitJ2qeQYNENzGBglROkCl2y6GlxAG80IBtReCUp/xOSdlwDR0eim+SNkdStvmQM5IcWBuDKwGZc1A4v/UoLl7niV9fpl4X6bUX8lZzY4gidJOafoJ30VoY/lYGkrkEuz3GpbbT5v8fF3iXVRlEqhlpe8JSGu7Rd2cPcJSkQ1Cuj/QRhHPhFMF2KhTEf95c9ZBKI8H7SvBi7eLXfSW2Y0ve6vXBZKyjK9whgCU9iVOsJjqRXpAccaWOKi420CjmS0+uwj/Xr2wLZhPEjBA/G6Od30+eG9mICmbp/5wAGhK/ZxCT17ZETyFmOMo49jl9pxdKocJNuzMrLpSz7/g5Jwp8+y8Ck5YP7AX0R/dVA0t37DO7nAbQT5XVSYpMVh/yvpYJ9WR+tb8Yg1h2lERLR2fbuhQRcwmisZR2W3Sr2b7hX9MCMkMQw8y2fDJrzLrqKqkHcjvnI/TdzZW2MzeQDoBBb3fmgvjYg07l4kThS73wGX992w2Y+a1A2iirSmrYEm9dSh16JmXa8boGQAONQzQkHh7vpw0IBs9cnvqO1QLB1GtbBztUBXonA4TxMKLYZkVrrd2RhrYWMsDp7MpC4M0p/DA3E/qscYwq1OpwriewNdx6XXqMZbdUNqMP2viBY2VSGmNdHtVfbN/rnaeJetFGX7XgTVYD7wDq8TW9yseCK944jcT+y/o0YiT9j3OLQ2Ts0LDTQskpJSxRmXEQGy3NBDOYFTvRkcGJEQJItuol8NivJN1H9LoLIUAlAHBZxfHpUYx66YnP4PdTdMIWH+nxyekKPFfAT7olQ=";
$password = "12345678a";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  FACTURA DE INGRESO CON IMPUESTOS LOCALES (CEDULAR + ISH) POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto CEDULAR + ISH por valores
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
    //         'tin' => "FUNK671228PH6",
    //         'legalName' => "KARLA FUENTE NOLASCO",
    //         'taxRegimeCode' => "621",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $base64Cert,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $base64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'zipCode' => "42501",
    //         'taxRegimeCode' => "601",
    //         'cfdiUseCode' => "G01",
    //         'email' => "someone@somewhere.com"
    //     ],
    //     'items' => [
    //         // Item 1
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 9.5,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio",
    //             'description' => "Invoicing software as a service",
    //             'unitPrice' => 3587.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301697",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         // Item 2
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 8.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio2",
    //             'description' => "Software Consultant",
    //             'unitPrice' => 250.85,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301698",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         // Item 3
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 6.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio3",
    //             'description' => "Computer software",
    //             'unitPrice' => 1250.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301699",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.106666",
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
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
    //  FACTURA DE INGRESO CON IMPUESTO CEDULAR (SOLO) POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto CEDULAR por valores
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
    //         'tin' => "FUNK671228PH6",
    //         'legalName' => "KARLA FUENTE NOLASCO",
    //         'taxRegimeCode' => "621",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $base64Cert,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $base64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'zipCode' => "42501",
    //         'taxRegimeCode' => "601",
    //         'cfdiUseCode' => "G01",
    //         'email' => "someone@somewhere.com"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 9.5,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio",
    //             'description' => "Invoicing software as a service",
    //             'unitPrice' => 3587.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301697",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 8.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio2",
    //             'description' => "Software Consultant",
    //             'unitPrice' => 250.85,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301698",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 6.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio3",
    //             'description' => "Computer software",
    //             'unitPrice' => 1250.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301699",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.106666",
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
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
    //  FACTURA DE INGRESO CON IMPUESTO ISH (SOLO) POR VALORES
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso con impuesto ISH por valores
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
    //         'tin' => "FUNK671228PH6",
    //         'legalName' => "KARLA FUENTE NOLASCO",
    //         'taxRegimeCode' => "621",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $base64Cert,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $base64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'zipCode' => "42501",
    //         'taxRegimeCode' => "601",
    //         'cfdiUseCode' => "G01",
    //         'email' => "someone@somewhere.com"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 9.5,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio",
    //             'description' => "Invoicing software as a service",
    //             'unitPrice' => 3587.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301697",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 8.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio2",
    //             'description' => "Software Consultant",
    //             'unitPrice' => 250.85,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301698",
    //             'discount' => 255.85,
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ],
    //         [
    //             'itemCode' => "01010101",
    //             'quantity' => 6.0,
    //             'unitOfMeasurementCode' => "E48",
    //             'unitOfMeasurement' => "Unidad de servicio3",
    //             'description' => "Computer software",
    //             'unitPrice' => 1250.75,
    //             'taxObjectCode' => "02",
    //             'itemSku' => "7506022301699",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.106666",
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
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
