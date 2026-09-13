<?php
declare(strict_types=1);

/**
 * Ejemplos del complemento Comercio Exterior 2.0 por referencias.
 *
 * El emisor, el receptor y los conceptos se envian como id de entidades ya registradas.
 * Un concepto por referencia lleva unicamente 'id' y 'quantity': la clave del SAT, la unidad
 * de medida, la descripcion, el valor unitario y los impuestos los aporta el producto.
 *
 * Al facturar por referencia el atributo NoIdentificacion del concepto toma el id del producto,
 * de modo que 'comercioExterior.mercancias[].noIdentificacion' debe llevar ese mismo id y no el
 * SKU del producto. Si no coinciden, la mercancia del complemento no corresponde a ningun
 * concepto del comprobante.
 *
 * Los comprobantes de traslado cuyos conceptos llevan valor unitario cero conservan sus
 * conceptos en linea: un producto exige un valor unitario mayor a cero y el concepto no puede
 * bajarlo.
 */

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

require_once __DIR__ . '/../vendor/autoload.php';


// Crea las configuraciones del cliente http fiscalapi.
$settings = new FiscalApiSettings(
    'https://test.fiscalapi.com',
    '<API_KEY>',
    '<TENANT_KEY>',
    false,
    false,
);

// Ids de las personas registradas en FiscalAPI.
// El emisor debe tener su par de sellos CSD cargado y el receptor su uso de CFDI configurado.
$issuerId = "<issuer-id>";                                  // ESCUELA KEMPER URGATE, regimen 601
$recipientId = "<recipient-id>";                            // receptor extranjero, regimen 616
$nationalRecipientId = "<national-recipient-id>";           // receptor con RFC mexicano, regimen 601

// Ids de los productos registrados en FiscalAPI.
// Cada producto define clave del SAT, unidad de medida, valor unitario, objeto de impuesto
// e impuestos, de modo que el concepto solo necesita referirlo.
$productoFleteId = "<producto-flete-id>";                                   // FLETE, IVA trasladado e IEPS retenido
$productoGomitasId = "<producto-gomitas-id>";                               // Gomitas, IVA trasladado
$productoPulparindoId = "<producto-pulparindo-id>";                         // Pulparindo, IVA trasladado
$productoCigarrosId = "<producto-cigarros-id>";                             // Cigarros, IVA trasladado, ISR e IVA retenidos
$productoCigarrosDosImpuestosId = "<producto-cigarros-dos-impuestos-id>";   // Cigarros, IVA trasladado e ISR retenido
$productoBebidaId = "<producto-bebida-id>";                                 // Bebida, IVA trasladado, ISR e IVA retenidos
$productoFormulaMagistralId = "<producto-formula-magistral-id>";            // FORMULA MAGISTRAL, sin impuestos
$productoCigarrosSinImpuestosId = "<producto-cigarros-sin-impuestos-id>";   // Cigarros, sin impuestos

// Tipo de cambio del dolar publicado por el DOF para la fecha del comprobante.
// Si no corresponde, el SAT responde CCE121 e indica el valor esperado en el mensaje.
$tipoCambioUSD = "16.9722";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  EJEMPLO 1: FACTURA DE INGRESO USD CON COMERCIO EXTERIOR Y CARTA PORTE
    // ==================================================================

    // ------------------------------------------------------------------
    // Tres conceptos por referencia y dos mercancias en el complemento.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $recipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoFleteId,
    //             'quantity' => 1.000000
    //         ],
    //         [
    //             'id' => $productoGomitasId,
    //             'quantity' => 1.000000
    //         ],
    //         [
    //             'id' => $productoPulparindoId,
    //             'quantity' => 1.000000
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
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoGomitasId,
    //                     'fraccionArancelariaId' => "4011101099",
    //                     'cantidadAduana' => "1.000",
    //                     'unidadAduanaId' => "06",
    //                     'valorUnitarioAduana' => "120.00",
    //                     'valorDolares' => "120.00"
    //                 ],
    //                 [
    //                     'noIdentificacion' => $productoPulparindoId,
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
    // El producto aporta el IVA trasladado y las retenciones de ISR e IVA.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $recipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoCigarrosId,
    //             'quantity' => 2
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoCigarrosId,
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
    // Dos conceptos del mismo producto consolidados en una sola mercancia.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $recipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoFormulaMagistralId,
    //             'quantity' => 1.0
    //         ],
    //         [
    //             'id' => $productoFormulaMagistralId,
    //             'quantity' => 1.0
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoFormulaMagistralId,
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
    // Caso minimo de exportacion con el concepto tomado del catalogo de productos.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $recipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoCigarrosDosImpuestosId,
    //             'quantity' => 2
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoCigarrosDosImpuestosId,
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
    // Exportacion facturada a un RFC mexicano registrado como persona.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $nationalRecipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoCigarrosId,
    //             'quantity' => 2
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoCigarrosId,
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
    // Conceptos en linea: llevan valor unitario cero, que un producto no admite.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $issuerId
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
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    // Concepto en linea: lleva valor unitario cero, que un producto no admite.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $issuerId
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
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    // Traslado con el concepto tomado de un producto sin impuestos.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $issuerId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoCigarrosSinImpuestosId,
    //             'quantity' => 2
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoCigarrosSinImpuestosId,
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
    // Un concepto por referencia con sus tres impuestos definidos en el producto.
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
    //         'id' => $issuerId
    //     ],
    //     'recipient' => [
    //         'id' => $recipientId
    //     ],
    //     'items' => [
    //         [
    //             'id' => $productoBebidaId,
    //             'quantity' => 1.000
    //         ]
    //     ],
    //     'complement' => [
    //         'comercioExterior' => [
    //             'claveDePedimentoId' => "A1",
    //             'certificadoOrigen' => 0,
    //             'incotermId' => "FOB",
    //             'tipoCambioUSD' => $tipoCambioUSD,
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
    //                     'noIdentificacion' => $productoBebidaId,
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
