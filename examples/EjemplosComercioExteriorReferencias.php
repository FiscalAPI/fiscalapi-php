<?php
declare(strict_types=1);

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

require_once __DIR__ . '/../vendor/autoload.php';


// Ejemplos del complemento Comercio Exterior 2.0 referenciando emisor y receptor por id.
//
// El emisor referenciado debe tener sus sellos CSD cargados y el receptor debe tener uso de
// CFDI configurado. Cuando el receptor se envia por id, la API toma de la persona su RFC,
// nombre, codigo postal, regimen fiscal, pais de residencia y registro de identidad tributaria.
//
// Los campos decimales sensibles al SAT viajan como string con los decimales literales
// ('taxRate' => "0.160000", 'tipoCambioUSD' => "16.9722", 'valorDolares' => "120.00").
// json_encode descarta los ceros finales de un float, y el SAT rechaza el comprobante con
// CFDI40179 en cfdi:TasaOCuota o CCE122 en cce20:TotalUSD cuando la escala no corresponde.
//
// 'tipoCambioUSD' debe ser el tipo de cambio publicado por el DOF para la fecha del comprobante.
// Actualizalo antes de ejecutar cualquier ejemplo o el SAT responde CCE121.

// Crea las configuraciones del cliente http fiscalapi.
$settings = new FiscalApiSettings(
    'https://test.fiscalapi.com',
    '<apiKey>',
    '<tenant>',
    false,
    false,
);

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  EJEMPLO 1: FACTURA DE INGRESO USD CON COMERCIO EXTERIOR Y CARTA PORTE
    // ==================================================================

    // ------------------------------------------------------------------
    // Exportacion definitiva con traslado internacional de salida. Tres conceptos, dos mercancias.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "99",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "USD",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "f8680ef5-49cb-4433-a604-3775c32e35a6"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "SERV02",
    //             'quantity' => 1.000000,
    //             'unitOfMeasurementCode' => "HUR",
    //             'description' => "FLETE",
    //             'unitPrice' => 2300.000000,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "003",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.300000",
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ],
    //         [
    //             'itemCode' => "50161509",
    //             'itemSku' => "A0001",
    //             'quantity' => 1.000000,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Gomitas",
    //             'unitPrice' => 120.000000,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
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
    //             'itemCode' => "50307037",
    //             'itemSku' => "A0002",
    //             'quantity' => 1.000000,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Pulparindo",
    //             'unitPrice' => 100.000000,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ]
    //             ]
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "ALB",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 120.00,
    //             'unidadPesoId' => "KGM",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR000001",
    //                     'rfcRemitenteDestinatario' => "XAXX010101000",
    //                     'nombreRemitenteDestinatario' => "Origen Nacional",
    //                     'fechaHoraSalidaLlegada' => "2026-04-27T08:00:00",
    //                     'domicilio' => [
    //                         'calle' => "xola",
    //                         'numeroExterior' => "531",
    //                         'coloniaId' => "0496",
    //                         'localidadId' => "03",
    //                         'municipioId' => "014",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "03100"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE000001",
    //                     'rfcRemitenteDestinatario' => "XAXX010101000",
    //                     'nombreRemitenteDestinatario' => "Destino Nacional",
    //                     'fechaHoraSalidaLlegada' => "2026-04-27T20:00:00",
    //                     'distanciaRecorrida' => 120.00,
    //                     'domicilio' => [
    //                         'calle' => "Av Coyoacan",
    //                         'numeroExterior' => "120",
    //                         'coloniaId' => "2624",
    //                         'localidadId' => "03",
    //                         'municipioId' => "014",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "03100"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "50433238",
    //                     'descripcion' => "Gomitas",
    //                     'cantidad' => 1,
    //                     'claveUnidadId' => "XPK",
    //                     'pesoEnKg' => 10.000,
    //                     'valorMercancia' => 1200.00,
    //                     'monedaId' => "USD",
    //                     'fraccionArancelariaId' => "2005800100",
    //                     'tipoMateriaId' => "04"
    //                 ],
    //                 [
    //                     'bienesTranspId' => "50433238",
    //                     'descripcion' => "Pulparindo",
    //                     'cantidad' => 1,
    //                     'claveUnidadId' => "XPK",
    //                     'pesoEnKg' => 10.000,
    //                     'valorMercancia' => 1000.00,
    //                     'monedaId' => "USD",
    //                     'fraccionArancelariaId' => "2005800100",
    //                     'tipoMateriaId' => "04"
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF02",
    //                 'numPermisoSCT' => "123456",
    //                 'configVehicularId' => "C2",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "555TTT",
    //                 'anioModeloVM' => 2023,
    //                 'aseguraRespCivil' => "ODISEA",
    //                 'polizaRespCivil' => "3456YUHNB234RT"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "KAHO641101B39",
    //                     'numLicencia' => "D0908240",
    //                     'nombreFigura' => "OSCAR KALA HAAK"
    //                 ]
    //             ]
    //         ],
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "CIF",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "Av Siempre viva",
    //                     'numeroExterior' => "123",
    //                     'coloniaId' => "0001",
    //                     'localidadId' => "06",
    //                     'municipioId' => "025",
    //                     'estadoId' => "COA",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "26015"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "Clinton ST",
    //                     'numeroExterior' => "10002",
    //                     'estado' => "NY",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "10002-0000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "A0001",
    //                     'fraccionArancelariaId' => "4011101099",
    //                     'cantidadAduana' => "1.000",
    //                     'unidadAduanaId' => "06",
    //                     'valorUnitarioAduana' => "120.00",
    //                     'valorDolares' => "120.00"
    //                 ],
    //                 [
    //                     'noIdentificacion' => "A0002",
    //                     'fraccionArancelariaId' => "8407210299",
    //                     'cantidadAduana' => "1.000",
    //                     'unidadAduanaId' => "06",
    //                     'valorUnitarioAduana' => "100.00",
    //                     'valorDolares' => "100.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 2: FACTURA DE INGRESO MXN CON COMERCIO EXTERIOR Y RETENCIONES
    // ==================================================================

    // ------------------------------------------------------------------
    // Exportacion facturada en pesos con IVA trasladado, ISR e IVA retenidos.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "99",
    //     'paymentMethodCode' => "PPD",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "f8680ef5-49cb-4433-a604-3775c32e35a6"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50211503",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 2,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Cigarros",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "001",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.100000",
    //                     'taxFlagCode' => "R"
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
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'numRegIdTrib' => "123456789",
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "2.00",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "11.74",
    //                     'valorDolares' => "23.47"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 3: FACTURA DE INGRESO MXN CON DOS CONCEPTOS Y UNA MERCANCIA
    // ==================================================================

    // ------------------------------------------------------------------
    // Dos conceptos del comprobante consolidados en una sola mercancia del complemento.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "f8680ef5-49cb-4433-a604-3775c32e35a6"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "51241200",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 1.0,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "FORMULA MAGISTRAL",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ],
    //         [
    //             'itemCode' => "51241200",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 1.0,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "FORMULA MAGISTRAL",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "2",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "10.00",
    //                     'valorDolares' => "20.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 4: FACTURA DE INGRESO USD CON RECEPTOR EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Caso minimo de exportacion: receptor XEXX010101000 con countryId y foreignTin.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "99",
    //     'paymentMethodCode' => "PPD",
    //     'currencyCode' => "USD",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "f8680ef5-49cb-4433-a604-3775c32e35a6"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50211503",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 2,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Cigarros",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "001",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.100000",
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'numRegIdTrib' => "123456789",
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "117.64",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "3.40",
    //                     'valorDolares' => "400.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 5: FACTURA DE INGRESO USD CON RECEPTOR NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Exportacion facturada a un RFC mexicano: el receptor no lleva countryId ni foreignTin.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "99",
    //     'paymentMethodCode' => "PPD",
    //     'currencyCode' => "USD",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "4f7daa04-a05f-41e9-8d36-33430d607ace"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50211503",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 2,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Cigarros",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "001",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.100000",
    //                     'taxFlagCode' => "R"
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
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'colonia' => "0214",
    //                     'localidad' => "01",
    //                     'municipio' => "014",
    //                     'estado' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostal' => "76199"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "117.64",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "3.40",
    //                     'valorDolares' => "400.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 6: COMPROBANTE DE TRASLADO CON COMERCIO EXTERIOR Y CARTA PORTE
    // ==================================================================

    // ------------------------------------------------------------------
    // Traslado en moneda XXX. El receptor del comprobante es el propio emisor.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "TR01",
    //             'quantity' => 1.0,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "TRANSPORTE DE CARGA",
    //             'unitPrice' => 0.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ],
    //         [
    //             'itemCode' => "32101622",
    //             'itemSku' => "UT421511",
    //             'quantity' => 100.00,
    //             'unitOfMeasurementCode' => "XBX",
    //             'description' => "MEMORIA FLASH",
    //             'unitPrice' => 0.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "ALB",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 120.00,
    //             'unidadPesoId' => "KGM",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR000001",
    //                     'rfcRemitenteDestinatario' => "XAXX010101000",
    //                     'nombreRemitenteDestinatario' => "Origen Nacional",
    //                     'fechaHoraSalidaLlegada' => "2026-04-27T08:00:00",
    //                     'domicilio' => [
    //                         'calle' => "xola",
    //                         'numeroExterior' => "531",
    //                         'coloniaId' => "0496",
    //                         'localidadId' => "03",
    //                         'municipioId' => "014",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "03100"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE000001",
    //                     'rfcRemitenteDestinatario' => "XAXX010101000",
    //                     'nombreRemitenteDestinatario' => "Destino Nacional",
    //                     'fechaHoraSalidaLlegada' => "2026-04-27T20:00:00",
    //                     'distanciaRecorrida' => 120.00,
    //                     'domicilio' => [
    //                         'calle' => "Av Coyoacan",
    //                         'numeroExterior' => "120",
    //                         'coloniaId' => "2624",
    //                         'localidadId' => "03",
    //                         'municipioId' => "014",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "03100"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "50433238",
    //                     'descripcion' => "Gomitas",
    //                     'cantidad' => 1,
    //                     'claveUnidadId' => "XPK",
    //                     'pesoEnKg' => 10.000,
    //                     'valorMercancia' => 1200.00,
    //                     'monedaId' => "USD",
    //                     'fraccionArancelariaId' => "2005800100",
    //                     'tipoMateriaId' => "04"
    //                 ],
    //                 [
    //                     'bienesTranspId' => "50433238",
    //                     'descripcion' => "Pulparindo",
    //                     'cantidad' => 1,
    //                     'claveUnidadId' => "XPK",
    //                     'pesoEnKg' => 10.000,
    //                     'valorMercancia' => 1000.00,
    //                     'monedaId' => "USD",
    //                     'fraccionArancelariaId' => "2005800100",
    //                     'tipoMateriaId' => "04"
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF02",
    //                 'numPermisoSCT' => "123456",
    //                 'configVehicularId' => "C2",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "555TTT",
    //                 'anioModeloVM' => 2023,
    //                 'aseguraRespCivil' => "ODISEA",
    //                 'polizaRespCivil' => "3456YUHNB234RT"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "KAHO641101B39",
    //                     'numLicencia' => "D0908240",
    //                     'nombreFigura' => "OSCAR KALA HAAK"
    //                 ]
    //             ]
    //         ],
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "UT421511",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "100.00",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "1.00",
    //                     'valorDolares' => "0.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 7: COMPROBANTE DE TRASLADO CON DESTINATARIO Y MOTIVO DE TRASLADO
    // ==================================================================

    // ------------------------------------------------------------------
    // Traslado con motivoTrasladoId y un destinatario con su domicilio en el extranjero.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "USD",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50211503",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 1.0,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Descripción",
    //             'unitPrice' => 0.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'motivoTrasladoId' => "02",
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FCA",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "SW Street.",
    //                     'numeroExterior' => "12345",
    //                     'localidad' => "Oregon",
    //                     'estado' => "OR",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "12345"
    //                 ]
    //             ],
    //             'destinatarios' => [
    //                 [
    //                     'numRegIdTrib' => "123456789",
    //                     'nombre' => "EKU9003173C9",
    //                     'domicilios' => [
    //                         [
    //                             'calle' => "SW Street.",
    //                             'numeroExterior' => "12345",
    //                             'localidad' => "Oregon",
    //                             'estado' => "OR",
    //                             'paisId' => "USA",
    //                             'codigoPostal' => "12345"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "0101210100",
    //                     'cantidadAduana' => "1",
    //                     'unidadAduanaId' => "07",
    //                     'valorUnitarioAduana' => "22.64",
    //                     'valorDolares' => "22.64"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 8: COMPROBANTE DE TRASLADO USD SIN CARTA PORTE
    // ==================================================================

    // ------------------------------------------------------------------
    // Traslado simple con complemento de Comercio Exterior unicamente.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "USD",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50211503",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 2,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Cigarros",
    //             'unitPrice' => 200.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2402200100",
    //                     'cantidadAduana' => "117.64",
    //                     'unidadAduanaId' => "01",
    //                     'valorUnitarioAduana' => "3.40",
    //                     'valorDolares' => "400.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 9: FACTURA DE INGRESO USD CON UNA MERCANCIA
    // ==================================================================

    // ------------------------------------------------------------------
    // Exportacion de un solo concepto con su mercancia correspondiente.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "99",
    //     'paymentMethodCode' => "PPD",
    //     'currencyCode' => "USD",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'paymentConditions' => "CondicionesDePago",
    //     'exportCode' => "02",
    //     'issuer' => [
    //         'id' => "b2c921d7-dd04-41d2-ad51-956cd2e8ebe9"
    //     ],
    //     'recipient' => [
    //         'id' => "f8680ef5-49cb-4433-a604-3775c32e35a6"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "50201708",
    //             'itemSku' => "131494-1055",
    //             'quantity' => 1.000,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Bebida",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.160000",
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "001",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => "0.100000",
    //                     'taxFlagCode' => "R"
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
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => "16.9722",
    //             'emisor' => [
    //                 'domicilio' => [
    //                     'calle' => "CALLE DEL PAPEL",
    //                     'coloniaId' => "0214",
    //                     'localidadId' => "01",
    //                     'municipioId' => "014",
    //                     'estadoId' => "QUE",
    //                     'paisId' => "MEX",
    //                     'codigoPostalId' => "76199"
    //                 ]
    //             ],
    //             'receptor' => [
    //                 'numRegIdTrib' => "123456789",
    //                 'domicilio' => [
    //                     'calle' => "ST. A",
    //                     'estado' => "TX",
    //                     'paisId' => "USA",
    //                     'codigoPostal' => "00000"
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'noIdentificacion' => "131494-1055",
    //                     'fraccionArancelariaId' => "2009310201",
    //                     'cantidadAduana' => "0.500",
    //                     'unidadAduanaId' => "08",
    //                     'valorUnitarioAduana' => "200.00",
    //                     'valorDolares' => "100.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

} catch (\Exception $e) {
    consoleError($e);
}

function consoleLog(FiscalApiHttpResponseInterface $apiResponse) {
    echo "apiResponse:\n" . json_encode($apiResponse->getJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

function consoleError(\Exception $e) {
    echo "Error en la ejecucion: " . $e->getMessage() . "\n";
    echo "Traza: " . $e->getTraceAsString() . "\n";
}

/**
 * Obtiene la fecha y hora actual en la zona horaria de Mexico Central
 */
function getCurrentDate(): string {
    date_default_timezone_set('Etc/GMT+6');
    return date('Y-m-d\TH:i:s');
}
