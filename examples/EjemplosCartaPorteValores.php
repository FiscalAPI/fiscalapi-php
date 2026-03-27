<?php
declare(strict_types=1);

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

// Contraseña del CSD
$password = "12345678a";
// Sellos de ESCUELA KEMPER URGATE
$escuelaKemperUrgateBase64Cer = "MIIFsDCCA5igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MTYwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTE0MzUxWhcNMjcwNTE4MTE0MzUxWjCB1zEnMCUGA1UEAxMeRVNDVUVMQSBLRU1QRVIgVVJHQVRFIFNBIERFIENWMScwJQYDVQQpEx5FU0NVRUxBIEtFTVBFUiBVUkdBVEUgU0EgREUgQ1YxJzAlBgNVBAoTHkVTQ1VFTEEgS0VNUEVSIFVSR0FURSBTQSBERSBDVjElMCMGA1UELRMcRUtVOTAwMzE3M0M5IC8gVkFEQTgwMDkyN0RKMzEeMBwGA1UEBRMVIC8gVkFEQTgwMDkyN0hTUlNSTDA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtmecO6n2GS0zL025gbHGQVxznPDICoXzR2uUngz4DqxVUC/w9cE6FxSiXm2ap8Gcjg7wmcZfm85EBaxCx/0J2u5CqnhzIoGCdhBPuhWQnIh5TLgj/X6uNquwZkKChbNe9aeFirU/JbyN7Egia9oKH9KZUsodiM/pWAH00PCtoKJ9OBcSHMq8Rqa3KKoBcfkg1ZrgueffwRLws9yOcRWLb02sDOPzGIm/jEFicVYt2Hw1qdRE5xmTZ7AGG0UHs+unkGjpCVeJ+BEBn0JPLWVvDKHZAQMj6s5Bku35+d/MyATkpOPsGT/VTnsouxekDfikJD1f7A1ZpJbqDpkJnss3vQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAFaUgj5PqgvJigNMgtrdXZnbPfVBbukAbW4OGnUhNrA7SRAAfv2BSGk16PI0nBOr7qF2mItmBnjgEwk+DTv8Zr7w5qp7vleC6dIsZFNJoa6ZndrE/f7KO1CYruLXr5gwEkIyGfJ9NwyIagvHHMszzyHiSZIA850fWtbqtythpAliJ2jF35M5pNS+YTkRB+T6L/c6m00ymN3q9lT1rB03YywxrLreRSFZOSrbwWfg34EJbHfbFXpCSVYdJRfiVdvHnewN0r5fUlPtR9stQHyuqewzdkyb5jTTw02D2cUfL57vlPStBj7SEi3uOWvLrsiDnnCIxRMYJ2UA2ktDKHk+zWnsDmaeleSzonv2CHW42yXYPCvWi88oE1DJNYLNkIjua7MxAnkNZbScNw01A6zbLsZ3y8G6eEYnxSTRfwjd8EP4kdiHNJftm7Z4iRU7HOVh79/lRWB+gd171s3d/mI9kte3MRy6V8MMEMCAnMboGpaooYwgAmwclI2XZCczNWXfhaWe0ZS5PmytD/GDpXzkX0oEgY9K/uYo5V77NdZbGAjmyi8cE2B2ogvyaN2XfIInrZPgEffJ4AB7kFA2mwesdLOCh0BLD9itmCve3A1FGR4+stO2ANUoiI3w3Tv2yQSg4bjeDlJ08lXaaFCLW2peEXMXjQUk7fmpb5MNuOUTW6BE=";
$escuelaKemperUrgateBase64Key = "MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIAgEAAoIBAQACAggAMBQGCCqGSIb3DQMHBAgwggS/AgEAMASCBMh4EHl7aNSCaMDA1VlRoXCZ5UUmqErAbucoZQObOaLUEm+I+QZ7Y8Giupo+F1XWkLvAsdk/uZlJcTfKLJyJbJwsQYbSpLOCLataZ4O5MVnnmMbfG//NKJn9kSMvJQZhSwAwoGLYDm1ESGezrvZabgFJnoQv8Si1nAhVGTk9FkFBesxRzq07dmZYwFCnFSX4xt2fDHs1PMpQbeq83aL/PzLCce3kxbYSB5kQlzGtUYayiYXcu0cVRu228VwBLCD+2wTDDoCmRXtPesgrLKUR4WWWb5N2AqAU1mNDC+UEYsENAerOFXWnmwrcTAu5qyZ7GsBMTpipW4Dbou2yqQ0lpA/aB06n1kz1aL6mNqGPaJ+OqoFuc8Ugdhadd+MmjHfFzoI20SZ3b2geCsUMNCsAd6oXMsZdWm8lzjqCGWHFeol0ik/xHMQvuQkkeCsQ28PBxdnUgf7ZGer+TN+2ZLd2kvTBOk6pIVgy5yC6cZ+o1Tloql9hYGa6rT3xcMbXlW+9e5jM2MWXZliVW3ZhaPjptJFDbIfWxJPjz4QvKyJk0zok4muv13Iiwj2bCyefUTRz6psqI4cGaYm9JpscKO2RCJN8UluYGbbWmYQU+Int6LtZj/lv8p6xnVjWxYI+rBPdtkpfFYRp+MJiXjgPw5B6UGuoruv7+vHjOLHOotRo+RdjZt7NqL9dAJnl1Qb2jfW6+d7NYQSI/bAwxO0sk4taQIT6Gsu/8kfZOPC2xk9rphGqCSS/4q3Os0MMjA1bcJLyoWLp13pqhK6bmiiHw0BBXH4fbEp4xjSbpPx4tHXzbdn8oDsHKZkWh3pPC2J/nVl0k/yF1KDVowVtMDXE47k6TGVcBoqe8PDXCG9+vjRpzIidqNo5qebaUZu6riWMWzldz8x3Z/jLWXuDiM7/Yscn0Z2GIlfoeyz+GwP2eTdOw9EUedHjEQuJY32bq8LICimJ4Ht+zMJKUyhwVQyAER8byzQBwTYmYP5U0wdsyIFitphw+/IH8+v08Ia1iBLPQAeAvRfTTIFLCs8foyUrj5Zv2B/wTYIZy6ioUM+qADeXyo45uBLLqkN90Rf6kiTqDld78NxwsfyR5MxtJLVDFkmf2IMMJHTqSfhbi+7QJaC11OOUJTD0v9wo0X/oO5GvZhe0ZaGHnm9zqTopALuFEAxcaQlc4R81wjC4wrIrqWnbcl2dxiBtD73KW+wcC9ymsLf4I8BEmiN25lx/OUc1IHNyXZJYSFkEfaxCEZWKcnbiyf5sqFSSlEqZLc4lUPJFAoP6s1FHVcyO0odWqdadhRZLZC9RCzQgPlMRtji/OXy5phh7diOBZv5UYp5nb+MZ2NAB/eFXm2JLguxjvEstuvTDmZDUb6Uqv++RdhO5gvKf/AcwU38ifaHQ9uvRuDocYwVxZS2nr9rOwZ8nAh+P2o4e0tEXjxFKQGhxXYkn75H3hhfnFYjik/2qunHBBZfcdG148MaNP6DjX33M238T9Zw/GyGx00JMogr2pdP4JAErv9a5yt4YR41KGf8guSOUbOXVARw6+ybh7+meb7w4BeTlj3aZkv8tVGdfIt3lrwVnlbzhLjeQY6PplKp3/a5Kr5yM0T4wJoKQQ6v3vSNmrhpbuAtKxpMILe8CQoo=";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  EJEMPLO 1: FACTURA INGRESO AUTOTRANSPORTE NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso autotransporte nacional (sin impuestos)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "URE180429TM6",
    //         'legalName' => "UNIVERSIDAD ROBOTICA ESPAÑOLA",
    //         'zipCode' => "86991",
    //         'taxRegimeCode' => "601",
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "URE180429TM6",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "URE180429TM6",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "URE180429TM6",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "NumeroExterior1",
    //                         'numeroInterior' => "NumeroInterior1",
    //                         'coloniaId' => "Colonia1",
    //                         'localidadId' => "Localidad1",
    //                         'referencia' => "Referencia1",
    //                         'municipioId' => "Municipio1",
    //                         'estadoId' => "Estado1",
    //                         'paisId' => "AFG",
    //                         'codigoPostalId' => "CodigoPosta1"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 2: FACTURA INGRESO AUTOTRANSPORTE NACIONAL CON IMPUESTOS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso autotransporte nacional con impuestos
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
    //                 'fileType' => 1,
    //                 'password' => $password
    //             ]
    //         ]
    //     ],
    //     'recipient' => [
    //         'tin' => "URE180429TM6",
    //         'legalName' => "UNIVERSIDAD ROBOTICA ESPAÑOLA",
    //         'zipCode' => "86991",
    //         'taxRegimeCode' => "601",
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 26232.75,
    //             'discount' => 0,
    //             'taxObjectCode' => "02",
    //             'itemTaxes' => [
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => 0.160000,
    //                     'taxFlagCode' => "T"
    //                 ],
    //                 [
    //                     'taxCode' => "002",
    //                     'taxTypeCode' => "Tasa",
    //                     'taxRate' => 0.040000,
    //                     'taxFlagCode' => "R"
    //                 ]
    //             ]
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "URE180429TM6",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "URE180429TM6",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "URE180429TM6",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "NumeroExterior1",
    //                         'numeroInterior' => "NumeroInterior1",
    //                         'coloniaId' => "Colonia1",
    //                         'localidadId' => "Localidad1",
    //                         'referencia' => "Referencia1",
    //                         'municipioId' => "Municipio1",
    //                         'estadoId' => "Estado1",
    //                         'paisId' => "AFG",
    //                         'codigoPostalId' => "CodigoPosta1"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 3: FACTURA INGRESO AUTOTRANSPORTE EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso autotransporte extranjero (salida)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 4: FACTURA INGRESO AUTOTRANSPORTE INTERNACIONAL ADUANERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso autotransporte internacional aduanero (entrada)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 5: FACTURA INGRESO TRANSPORTE FERROVIARIO NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte ferroviario nacional
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "JM047",
    //                     'nombreEstacion' => "HUEHUETOCA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 6: FACTURA INGRESO TRANSPORTE FERROVIARIO EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte ferroviario extranjero (salida)
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "04",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "EF0001",
    //                     'nombreEstacion' => "NombreEstacion",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "1234",
    //                         'coloniaId' => "1234",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "1234",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 7: FACTURA INGRESO TRANSPORTE FERROVIARIO INTERNACIONAL ADUANERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte ferroviario internacional aduanero (entrada)
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "04",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "JM047",
    //                     'nombreEstacion' => "HUEHUETOCA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 8: FACTURA INGRESO TRANSPORTE AÉREO NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte aéreo nacional
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "EA0418",
    //                     'nombreEstacion' => "Los Cabos",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 9: FACTURA INGRESO TRANSPORTE AÉREO EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte aéreo extranjero (salida)
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "03",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "EA0143",
    //                     'nombreEstacion' => "Phoenix-Mesa Gateway",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "12344",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 10: FACTURA INGRESO TRANSPORTE AÉREO INTERNACIONAL ADUANERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte aéreo internacional aduanero (entrada)
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "03",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "EA0418",
    //                     'nombreEstacion' => "Los Cabos",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 11: FACTURA INGRESO TRANSPORTE MARÍTIMO NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte marítimo nacional
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "Tu",
    //                         'pesoBruto' => 1,
    //                         'pesoNeto' => 1,
    //                         'pesoTara' => 0.001,
    //                         'numPiezas' => 1
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 12: FACTURA INGRESO TRANSPORTE MARÍTIMO EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte marítimo extranjero (salida)
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
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "02",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "PM120",
    //                     'nombreEstacion' => "NombreEstacion",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "12345",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "N/A",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "Tu",
    //                         'pesoBruto' => 1,
    //                         'pesoNeto' => 1,
    //                         'pesoTara' => 0.001,
    //                         'numPiezas' => 1
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 13: FACTURA INGRESO TRANSPORTE MARÍTIMO INTERNACIONAL ADUANERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de ingreso transporte marítimo internacional aduanero (entrada)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'paymentFormCode' => "01",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'typeCode' => "I",
    //     'expeditionZipCode' => "42501",
    //     'series' => "CP3.1",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "01",
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ],
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1.50,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020",
    //                             'cvesTransporteId' => "02"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "X1A",
    //                         'pesoBruto' => 1.50,
    //                         'pesoNeto' => 1.00,
    //                         'pesoTara' => 0.50
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "NumeroExterior1",
    //                         'numeroInterior' => "NumeroInterior1",
    //                         'coloniaId' => "Colonia1",
    //                         'localidadId' => "Localidad1",
    //                         'referencia' => "Referencia1",
    //                         'municipioId' => "Municipio1",
    //                         'estadoId' => "Estado1",
    //                         'paisId' => "AFG",
    //                         'codigoPostalId' => "CodigoPosta1"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 14: FACTURA TRASLADO AUTOTRANSPORTE NACIONAL
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de traslado autotransporte nacional
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 15: FACTURA TRASLADO AUTOTRANSPORTE EXTRANJERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de traslado autotransporte extranjero (salida)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  EJEMPLO 16: FACTURA TRASLADO AUTOTRANSPORTE INTERNACIONAL ADUANERO
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear factura de traslado autotransporte internacional aduanero (entrada)
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "SerieCCP31",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "01",
    //             'totalDistRec' => 1,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'logisticaInversaRecoleccionDevolucionId' => "Sí",
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'distanciaRecorrida' => 1,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2003-04-02T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'formaFarmaceuticaId' => "01",
    //                     'condicionesEspTranspId' => "01",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'fraccionArancelariaId' => "6309000100",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'autotransporte' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'configVehicularId' => "VL",
    //                 'pesoBrutoVehicular' => 1,
    //                 'placaVM' => "plac892",
    //                 'anioModeloVM' => 2020,
    //                 'aseguraRespCivil' => "AseguraRespCivil",
    //                 'polizaRespCivil' => "123456789",
    //                 'remolques' => [
    //                     [
    //                         'subTipoRemId' => "CTR004",
    //                         'placa' => "VL45K98"
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "NumLicencia1",
    //                     'nombreFigura' => "NombreFigura1",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "214",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "N/A"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 17: FACTURA TRASLADO TRANSPORTE FERROVIARIO NACIONAL
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte ferroviario, operación nacional, sin régimen
    // aduanero. Incluye 6 ubicaciones (1 origen + 5 destinos) con
    // estaciones ferroviarias, transporteFerroviario con derechosDePaso
    // y carros, y tiposFigura con partesTransporte.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "JM047",
    //                     'nombreEstacion' => "HUEHUETOCA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 18: FACTURA TRASLADO TRANSPORTE FERROVIARIO EXTRANJERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte ferroviario, operación internacional, salida
    // hacia USA, vía entrada/salida "04" (ferroviario), régimen aduanero
    // EXD. El último destino tiene RFC XEXX010101000 con residencia
    // fiscal USA y estación extranjera libre.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "04",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "EF0001",
    //                     'nombreEstacion' => "NombreEstacion",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "1234",
    //                         'coloniaId' => "1234",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "1234",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 19: FACTURA TRASLADO TRANSPORTE FERROVIARIO INTERNACIONAL ADUANERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte ferroviario, operación internacional, entrada
    // desde AFG, vía "04" (ferroviario), régimen aduanero IMD.
    // Incluye documentacionAduanera en la mercancía.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "04",
    //             'totalDistRec' => 500,
    //             'registroISTMOId' => "Sí",
    //             'ubicacionPoloOrigenId' => "01",
    //             'ubicacionPoloDestinoId' => "01",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "Q0736",
    //                     'nombreEstacion' => "SANTO NINO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202021",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "SC283",
    //                     'nombreEstacion' => "HUAXTITLA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T01:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202022",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TG0",
    //                     'nombreEstacion' => "NAVOJOA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T02:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202023",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "E0029",
    //                     'nombreEstacion' => "TRES JAGUEYES",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T03:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202024",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "TI032",
    //                     'nombreEstacion' => "NAVOLATO",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T04:00:01",
    //                     'tipoEstacionId' => "02",
    //                     'distanciaRecorrida' => 100.00
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202025",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "JM047",
    //                     'nombreEstacion' => "HUEHUETOCA",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T05:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'distanciaRecorrida' => 100.00,
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202025"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteFerroviario' => [
    //                 'tipoDeServicioId' => "TS01",
    //                 'tipoDeTraficoId' => "TT01",
    //                 'derechosDePaso' => [
    //                     [
    //                         'tipoDerechoDePasoId' => "CDP114",
    //                         'kilometrajePagado' => 100
    //                     ]
    //                 ],
    //                 'carros' => [
    //                     [
    //                         'tipoCarroId' => "TC08",
    //                         'matriculaCarro' => "A00012",
    //                         'guiaCarro' => "123ASD",
    //                         'toneladasNetasCarro' => 10
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 20: FACTURA TRASLADO TRANSPORTE AÉREO NACIONAL
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte aéreo, operación nacional. Dos ubicaciones
    // (origen aeropuerto Loreto, destino Los Cabos), transporteAereo
    // con datos de aeronave y embarcador, y tiposFigura tipo "01"
    // (operador) sin domicilio.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "EA0418",
    //                     'nombreEstacion' => "Los Cabos",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 21: FACTURA TRASLADO TRANSPORTE AÉREO EXTRANJERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte aéreo, operación internacional, salida hacia
    // USA, vía "03" (aéreo), régimen aduanero EXD. El destino tiene RFC
    // XEXX010101000 con residencia fiscal USA y aeropuerto extranjero
    // Phoenix-Mesa Gateway.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "03",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "EA0143",
    //                     'nombreEstacion' => "Phoenix-Mesa Gateway",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "12344",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "WHITE HOUSE",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 22: FACTURA TRASLADO TRANSPORTE AÉREO INTERNACIONAL ADUANERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte aéreo, operación internacional, entrada desde
    // AFG, vía "03" (aéreo), régimen aduanero IMD. Incluye
    // documentacionAduanera en la mercancía.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "03",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 10,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "EA0417",
    //                     'nombreEstacion' => "Loreto",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "EA0418",
    //                     'nombreEstacion' => "Los Cabos",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'transporteAereo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "Demo",
    //                 'matriculaAeronave' => "61E5-WZ",
    //                 'nombreAseg' => "NombreAseg",
    //                 'numPolizaSeguro' => "NumPolizaSeguro",
    //                 'numeroGuia' => "acUbYlBVTmlzx",
    //                 'lugarContrato' => "LugarContrato",
    //                 'codigoTransportistaId' => "CA001",
    //                 'rfcEmbarcador' => "EKU9003173C9",
    //                 'nombreEmbarcador' => "Embarcador"
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "01",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'numLicencia' => "a234567890",
    //                     'nombreFigura' => "NombreFigura"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 23: FACTURA TRASLADO TRANSPORTE MARÍTIMO NACIONAL
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte marítimo, operación nacional. Dos ubicaciones
    // con navegacionTraficoId "Altura", transporteMaritimo con datos
    // completos de embarcación, contenedores y remolquesCCP.
    // detalleMercancia incluida.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "No",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "Tu",
    //                         'pesoBruto' => 1,
    //                         'pesoNeto' => 1,
    //                         'pesoTara' => 0.001,
    //                         'numPiezas' => 1
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 24: FACTURA TRASLADO TRANSPORTE MARÍTIMO EXTRANJERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte marítimo, operación internacional, salida
    // hacia USA, vía "02" (marítimo), régimen aduanero EXD. El destino
    // tiene RFC XEXX010101000 con residencia fiscal USA y puerto
    // extranjero.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Salida",
    //             'paisOrigenDestinoId' => "USA",
    //             'viaEntradaSalidaId' => "02",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "EXD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "XEXX010101000",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numRegIdTrib' => "01010101",
    //                     'residenciaFiscalId' => "USA",
    //                     'numEstacionId' => "PM120",
    //                     'nombreEstacion' => "NombreEstacion",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'domicilio' => [
    //                         'calle' => "ST",
    //                         'numeroExterior' => "12345",
    //                         'coloniaId' => "N/A",
    //                         'referencia' => "N/A",
    //                         'municipioId' => "N/A",
    //                         'estadoId' => "TX",
    //                         'paisId' => "USA",
    //                         'codigoPostalId' => "12345"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "Tu",
    //                         'pesoBruto' => 1,
    //                         'pesoNeto' => 1,
    //                         'pesoTara' => 0.001,
    //                         'numPiezas' => 1
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);

    // ==================================================================
    //  EJEMPLO 25: FACTURA TRASLADO TRANSPORTE MARÍTIMO INTERNACIONAL ADUANERO
    // ==================================================================
    // ------------------------------------------------------------------
    // Traslado, transporte marítimo, operación internacional, entrada
    // desde AFG, vía "02" (marítimo), dos regímenes aduaneros IMD.
    // Incluye documentacionAduanera y detalleMercancia en la mercancía.
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'currencyCode' => "XXX",
    //     'typeCode' => "T",
    //     'expeditionZipCode' => "42501",
    //     'series' => "Serie",
    //     'date' => $currentDate,
    //     'exchangeRate' => 1,
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'tin' => "EKU9003173C9",
    //         'legalName' => "ESCUELA KEMPER URGATE",
    //         'taxRegimeCode' => "601",
    //         'taxCredentials' => [
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Cer,
    //                 'fileType' => 0,
    //                 'password' => $password
    //             ],
    //             [
    //                 'base64File' => $escuelaKemperUrgateBase64Key,
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
    //         'cfdiUseCode' => "S01"
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "78101800",
    //             'itemSku' => "UT421511",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "H87",
    //             'description' => "Transporte de carga por carretera",
    //             'unitPrice' => 100.00,
    //             'discount' => 0,
    //             'taxObjectCode' => "01",
    //             'itemTaxes' => []
    //         ]
    //     ],
    //     'complement' => [
    //         'cartaPorte' => [
    //             'transpInternacId' => "Sí",
    //             'entradaSalidaMercId' => "Entrada",
    //             'paisOrigenDestinoId' => "AFG",
    //             'viaEntradaSalidaId' => "02",
    //             'unidadPesoId' => "XBX",
    //             'pesoNetoTotal' => 1,
    //             'regimenAduaneros' => [
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ],
    //                 [
    //                     'regimenAduaneroId' => "IMD"
    //                 ]
    //             ],
    //             'ubicaciones' => [
    //                 [
    //                     'tipoUbicacion' => "Origen",
    //                     'idUbicacion' => "OR101010",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario1",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:00",
    //                     'tipoEstacionId' => "01",
    //                     'domicilio' => [
    //                         'calle' => "Calle1",
    //                         'numeroExterior' => "211",
    //                         'numeroInterior' => "212",
    //                         'coloniaId' => "1957",
    //                         'localidadId' => "13",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "011",
    //                         'estadoId' => "CMX",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "13250"
    //                     ]
    //                 ],
    //                 [
    //                     'tipoUbicacion' => "Destino",
    //                     'idUbicacion' => "DE202020",
    //                     'rfcRemitenteDestinatario' => "EKU9003173C9",
    //                     'nombreRemitenteDestinatario' => "NombreRemitenteDestinatario2",
    //                     'numEstacionId' => "PM001",
    //                     'nombreEstacion' => "Rosarito",
    //                     'navegacionTraficoId' => "Altura",
    //                     'fechaHoraSalidaLlegada' => "2023-08-01T00:00:01",
    //                     'tipoEstacionId' => "03",
    //                     'domicilio' => [
    //                         'calle' => "Calle2",
    //                         'numeroExterior' => "214",
    //                         'numeroInterior' => "215",
    //                         'coloniaId' => "0347",
    //                         'localidadId' => "23",
    //                         'referencia' => "casa negra",
    //                         'municipioId' => "004",
    //                         'estadoId' => "COA",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "25350"
    //                     ]
    //                 ]
    //             ],
    //             'mercancias' => [
    //                 [
    //                     'bienesTranspId' => "11121900",
    //                     'descripcion' => "Accesorios de equipo de telefonía",
    //                     'cantidad' => 1.0,
    //                     'claveUnidadId' => "XBX",
    //                     'materialPeligrosoId' => "No",
    //                     'denominacionGenericaProd' => "DenominacionGenericaProd1",
    //                     'denominacionDistintivaProd' => "DenominacionDistintivaProd1",
    //                     'fabricante' => "Fabricante1",
    //                     'fechaCaducidad' => "2028-01-01T00:00:00",
    //                     'loteMedicamento' => "LoteMedic1",
    //                     'registroSanitarioFolioAutorizacion' => "RegistroSanita1",
    //                     'pesoEnKg' => 1,
    //                     'valorMercancia' => 100,
    //                     'monedaId' => "MXN",
    //                     'tipoMateriaId' => "05",
    //                     'descripcionMateria' => "otramateria",
    //                     'documentacionAduanera' => [
    //                         [
    //                             'tipoDocumentoId' => "01",
    //                             'numPedimento' => "23  43  0472  8000448",
    //                             'rfcImpo' => "EKU9003173C9"
    //                         ]
    //                     ],
    //                     'cantidadTransporta' => [
    //                         [
    //                             'cantidad' => 1,
    //                             'idOrigen' => "OR101010",
    //                             'idDestino' => "DE202020"
    //                         ]
    //                     ],
    //                     'detalleMercancia' => [
    //                         'unidadPesoMercId' => "Tu",
    //                         'pesoBruto' => 1,
    //                         'pesoNeto' => 1,
    //                         'pesoTara' => 0.001,
    //                         'numPiezas' => 1
    //                     ]
    //                 ]
    //             ],
    //             'transporteMaritimo' => [
    //                 'permSCTId' => "TPAF01",
    //                 'numPermisoSCT' => "NumPermisoSCT1",
    //                 'nombreAseg' => "NombreAseg1",
    //                 'numPolizaSeguro' => "NumPolizaSeguro1",
    //                 'tipoEmbarcacionId' => "B01",
    //                 'matricula' => "Matricula1",
    //                 'numeroOMI' => "IMO1234567",
    //                 'anioEmbarcacion' => 2003,
    //                 'nombreEmbarc' => "NombreEmbarc1",
    //                 'nacionalidadEmbarcId' => "AFG",
    //                 'unidadesDeArqBruto' => 0.001,
    //                 'tipoCargaId' => "CGS",
    //                 'eslora' => 0.01,
    //                 'manga' => 0.01,
    //                 'calado' => 0.01,
    //                 'puntal' => 0.01,
    //                 'lineaNaviera' => "LineaNaviera1",
    //                 'nombreAgenteNaviero' => "NombreAgenteNaviero1",
    //                 'numAutorizacionNavieroId' => "ANC001/2022",
    //                 'numViaje' => "NumViaje1",
    //                 'numConocEmbarc' => "NumConocEmbarc1",
    //                 'permisoTempNavegacion' => "PermisoTempNavegac1",
    //                 'contenedores' => [
    //                     [
    //                         'tipoContenedorId' => "CM011",
    //                         'idCCPRelacionado' => "CCCBCD94-870A-4332-A52A-A52AA52AA52A",
    //                         'placaVMCCP' => "JNG7683",
    //                         'fechaCertificacionCCP' => "2024-06-20T11:11:00",
    //                         'remolquesCCP' => [
    //                             [
    //                                 'subTipoRemCCPId' => "CTR001",
    //                                 'placaCCP' => "JNG7636"
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'tiposFigura' => [
    //                 [
    //                     'tipoFiguraId' => "02",
    //                     'rfcFigura' => "EKU9003173C9",
    //                     'nombreFigura' => "NombreFigura",
    //                     'partesTransporte' => [
    //                         [
    //                             'parteTransporteId' => "PT02"
    //                         ]
    //                     ],
    //                     'domicilio' => [
    //                         'calle' => "calle",
    //                         'numeroExterior' => "211",
    //                         'coloniaId' => "0814",
    //                         'localidadId' => "01",
    //                         'referencia' => "casa blanca",
    //                         'municipioId' => "010",
    //                         'estadoId' => "ZAC",
    //                         'paisId' => "MEX",
    //                         'codigoPostalId' => "99080"
    //                     ]
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
