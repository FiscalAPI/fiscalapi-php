<?php
declare(strict_types=1);

/**
 * Ejemplos de Nómina por Referencias
 *
 * Este archivo contiene ejemplos de cómo crear facturas de nómina (CFDI tipo "N")
 * usando referencias (IDs de personas previamente registradas en FiscalAPI).
 * Incluye 13 variantes: ordinaria, asimilados, bonos/fondo de ahorro, horas extra,
 * incapacidades, SNCF, extraordinaria (aguinaldo), separación/indemnización,
 * jubilación, sin deducciones, viáticos, subsidio causado y nómina simple.
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
$organicosNavezOsorioId = "ab7ec306-6f81-4f9f-b55f-bbbb1ab2f153";
$xochiltCasasChavezId = "acf43966-4672-48b6-a01a-d04cac6c3d64";
$ingridXodarJimenezId = "aa2ad8c3-6ec5-4601-91be-d827d9a865bc";

// Definir la fecha actual
$currentDate = getCurrentDate();

// Crear cliente HTTP
$client = new FiscalApiClient($settings);


try {

    // ==================================================================
    //  NÓMINA ORDINARIA POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina ordinaria por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'expeditionZipCode' => "42501",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2025-08-30T00:00:00",
    //             'initialPaymentDate' => "2025-07-31T00:00:00",
    //             'finalPaymentDate' => "2025-08-30T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "1003",
    //                         'concept' => "Sueldo nominal",
    //                         'taxedAmount' => "95030.00",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "005",
    //                         'code' => "5913",
    //                         'concept' => "Fondo de Ahorro Aportación Patrón",
    //                         'taxedAmount' => "0",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "1885",
    //                         'concept' => "Bono Ingles",
    //                         'taxedAmount' => "14254.50",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "029",
    //                         'code' => "1941",
    //                         'concept' => "Vales Despensa",
    //                         'taxedAmount' => "0",
    //                         'exemptAmount' => "3439"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "1824",
    //                         'concept' => "Herramientas Teletrabajo (telecom y prop. electri)",
    //                         'taxedAmount' => "273",
    //                         'exemptAmount' => "0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "002",
    //                         'code' => "5050",
    //                         'concept' => "Exceso de subsidio al empleo",
    //                         'taxedAmount' => "0",
    //                         'exemptAmount' => "0"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "5050",
    //                         'concept' => "exceso de subsidio al empleo",
    //                         'amount' => "0",
    //                         'subsidyCaused' => "0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "5003",
    //                     'concept' => "ISR Causado",
    //                     'amount' => "27645"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "5910",
    //                     'concept' => "Fondo de ahorro Empleado Inversión",
    //                     'amount' => "4412.46"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "5914",
    //                     'concept' => "Fondo de Ahorro Patrón Inversión",
    //                     'amount' => "4412.46"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1966",
    //                     'concept' => "Contribución póliza exceso GMM",
    //                     'amount' => "519.91"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1934",
    //                     'concept' => "Descuento Vales Despensa",
    //                     'amount' => "1"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "1942",
    //                     'concept' => "Vales Despensa Electrónico",
    //                     'amount' => "3439"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "1895",
    //                     'concept' => "IMSS",
    //                     'amount' => "2391.13"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA ASIMILADOS POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina asimilados por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "06880",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $xochiltCasasChavezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-02T00:00:00",
    //             'initialPaymentDate' => "2023-06-01T00:00:00",
    //             'finalPaymentDate' => "2023-06-02T00:00:00",
    //             'daysPaid' => 1,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "046",
    //                         'code' => "010046",
    //                         'concept' => "INGRESOS ASIMILADOS A SALARIOS",
    //                         'taxedAmount' => "111197.73",
    //                         'exemptAmount' => "0.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "020002",
    //                     'concept' => "ISR",
    //                     'amount' => "36197.73"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON BONOS, FONDO DE AHORRO Y DEDUCCIONES POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con bonos, fondo de ahorro y deducciones por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'items' => [
    //         [
    //             'itemCode' => "84111505",
    //             'itemSku' => "84111505",
    //             'quantity' => 1,
    //             'unitOfMeasurementCode' => "ACT",
    //             'description' => "Pago de nómina",
    //             'unitPrice' => "1842.82",
    //             'discount' => "608.71",
    //             'taxObjectCode' => "01"
    //         ]
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-06-11T00:00:00",
    //             'initialPaymentDate' => "2023-06-05T00:00:00",
    //             'finalPaymentDate' => "2023-06-11T00:00:00",
    //             'daysPaid' => 7,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "SP01",
    //                         'concept' => "SUELDO",
    //                         'taxedAmount' => "1210.30",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "010",
    //                         'code' => "SP02",
    //                         'concept' => "PREMIO PUNTUALIDAD",
    //                         'taxedAmount' => "121.03",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "029",
    //                         'code' => "SP03",
    //                         'concept' => "MONEDERO ELECTRONICO",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "269.43"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "010",
    //                         'code' => "SP04",
    //                         'concept' => "PREMIO DE ASISTENCIA",
    //                         'taxedAmount' => "121.03",
    //                         'exemptAmount' => "0.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "005",
    //                         'code' => "SP54",
    //                         'concept' => "APORTACION FONDO AHORRO",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "121.03"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "ISRSUB",
    //                         'concept' => "Subsidio ISR para empleo",
    //                         'amount' => "0.0",
    //                         'subsidyCaused' => "0.0",
    //                         'balanceCompensation' => [
    //                             'favorableBalance' => "0.0",
    //                             'year' => 2022,
    //                             'remainingFavorableBalance' => "0.0"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "ZA09",
    //                     'concept' => "APORTACION FONDO AHORRO",
    //                     'amount' => "121.03"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "ISR",
    //                     'concept' => "ISR",
    //                     'amount' => "36.57"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "IMSS",
    //                     'concept' => "Cuota de Seguridad Social EE",
    //                     'amount' => "30.08"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "ZA68",
    //                     'concept' => "DEDUCCION FDO AHORRO PAT",
    //                     'amount' => "121.03"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "018",
    //                     'code' => "ZA11",
    //                     'concept' => "APORTACION CAJA AHORRO",
    //                     'amount' => "300.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON HORAS EXTRA POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con horas extra por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "019",
    //                         'code' => "00100",
    //                         'concept' => "Horas Extra",
    //                         'taxedAmount' => "50.00",
    //                         'exemptAmount' => "50.00",
    //                         'overtime' => [
    //                             [
    //                                 'days' => 1,
    //                                 'hoursTypeCode' => "01",
    //                                 'extraHours' => 2,
    //                                 'amountPaid' => "100.00"
    //                             ]
    //                         ]
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200.00"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON INCAPACIDADES POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con incapacidades por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.80",
    //                         'exemptAmount' => "2191.20"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200.00"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100.00"
    //                 ]
    //             ],
    //             'disabilities' => [
    //                 [
    //                     'disabilityDays' => 1,
    //                     'disabilityTypeCode' => "01"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA CON SNCF POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina con SNCF por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "39074",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $organicosNavezOsorioId
    //     ],
    //     'recipient' => [
    //         'id' => $xochiltCasasChavezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-16T00:00:00",
    //             'initialPaymentDate' => "2023-05-01T00:00:00",
    //             'finalPaymentDate' => "2023-05-16T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P001",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "3322.20",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P540",
    //                         'concept' => "Compensacion",
    //                         'taxedAmount' => "100.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P550",
    //                         'concept' => "Compensación Garantizada Extraordinaria",
    //                         'taxedAmount' => "2200.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "038",
    //                         'code' => "P530",
    //                         'concept' => "Servicio Extraordinario",
    //                         'taxedAmount' => "200.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P506",
    //                         'concept' => "Otras Prestaciones",
    //                         'taxedAmount' => "1500.0",
    //                         'exemptAmount' => "0.0"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "P505",
    //                         'concept' => "Remuneración al Desempeño Legislativo",
    //                         'taxedAmount' => "17500.0",
    //                         'exemptAmount' => "0.0"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "002",
    //                         'code' => "o002",
    //                         'concept' => "Subsidio para el empleo efectivamente entregado al trabajador",
    //                         'amount' => "0.0",
    //                         'subsidyCaused' => "0.0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "D002",
    //                     'concept' => "ISR",
    //                     'amount' => "4716.61"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "004",
    //                     'code' => "D525",
    //                     'concept' => "Redondeo",
    //                     'amount' => "0.81"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "D510",
    //                     'concept' => "Cuota Trabajador ISSSTE",
    //                     'amount' => "126.78"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA EXTRAORDINARIA (AGUINALDO) POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina extraordinaria (aguinaldo) por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-04T00:00:00",
    //             'initialPaymentDate' => "2023-06-04T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "002",
    //                         'code' => "00500",
    //                         'concept' => "Gratificación Anual (Aguinaldo)",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SEPARACIÓN INDEMNIZACIÓN POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina separación indemnización por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-06-04T00:00:00",
    //             'initialPaymentDate' => "2023-05-05T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "023",
    //                         'code' => "00500",
    //                         'concept' => "Pagos por separación",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ],
    //                     [
    //                         'earningTypeCode' => "025",
    //                         'code' => "00900",
    //                         'concept' => "Indemnizaciones",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "500.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => [],
    //                 'severance' => [
    //                     'totalPaid' => "10500.00",
    //                     'yearsOfService' => 1,
    //                     'lastMonthlySalary' => "10000.00",
    //                     'accumulableIncome' => "10000.00",
    //                     'nonAccumulableIncome' => "0.00"
    //                 ]
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA JUBILACIÓN POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina jubilación por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "E",
    //             'paymentDate' => "2023-05-05T00:00:00",
    //             'initialPaymentDate' => "2023-06-04T00:00:00",
    //             'finalPaymentDate' => "2023-06-04T00:00:00",
    //             'daysPaid' => 30,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "039",
    //                         'code' => "00500",
    //                         'concept' => "Jubilaciones, pensiones o haberes de retiro",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "10000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => [],
    //                 'retirement' => [
    //                     'totalOneTime' => "10000.00",
    //                     'accumulableIncome' => "10000.00",
    //                     'nonAccumulableIncome' => "0.00"
    //                 ]
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SIN DEDUCCIONES POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina sin deducciones por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => []
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA VIÁTICOS POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina viáticos por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-09-26T00:00:00",
    //             'initialPaymentDate' => "2023-09-11T00:00:00",
    //             'finalPaymentDate' => "2023-09-26T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "050",
    //                         'code' => "050",
    //                         'concept' => "Viaticos",
    //                         'taxedAmount' => "0.00",
    //                         'exemptAmount' => "3000.00"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "081",
    //                     'code' => "081",
    //                     'concept' => "Ajuste en viaticos entregados al trabajador",
    //                     'amount' => "3000.00"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SUBSIDIO CAUSADO AL EMPLEO POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina subsidio causado al empleo por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => [
    //                     [
    //                         'otherPaymentTypeCode' => "007",
    //                         'code' => "0002",
    //                         'concept' => "ISR ajustado por subsidio",
    //                         'amount' => "145.80",
    //                         'subsidyCaused' => "0.0"
    //                     ]
    //                 ]
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "107",
    //                     'code' => "D002",
    //                     'concept' => "Ajuste al Subsidio Causado",
    //                     'amount' => "160.35"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "D002",
    //                     'concept' => "ISR",
    //                     'amount' => "145.80"
    //                 ]
    //             ]
    //         ]
    //     ]
    // ];
    // $apiResponse = $client->getInvoiceService()->create($invoice);
    // consoleLog($apiResponse);


    // ==================================================================
    //  NÓMINA SIMPLE POR REFERENCIAS
    // ==================================================================

    // ------------------------------------------------------------------
    // Crear nómina simple por referencias
    // ------------------------------------------------------------------
    // $invoice = [
    //     'versionCode' => "4.0",
    //     'series' => "F",
    //     'date' => $currentDate,
    //     'typeCode' => "N",
    //     'paymentMethodCode' => "PUE",
    //     'currencyCode' => "MXN",
    //     'expeditionZipCode' => "20000",
    //     'exportCode' => "01",
    //     'issuer' => [
    //         'id' => $escuelaKemperUrgateId
    //     ],
    //     'recipient' => [
    //         'id' => $ingridXodarJimenezId
    //     ],
    //     'complement' => [
    //         'payroll' => [
    //             'version' => "1.2",
    //             'payrollTypeCode' => "O",
    //             'paymentDate' => "2023-05-24T00:00:00",
    //             'initialPaymentDate' => "2023-05-09T00:00:00",
    //             'finalPaymentDate' => "2023-05-24T00:00:00",
    //             'daysPaid' => 15,
    //             'earnings' => [
    //                 'earnings' => [
    //                     [
    //                         'earningTypeCode' => "001",
    //                         'code' => "00500",
    //                         'concept' => "Sueldos, Salarios Rayas y Jornales",
    //                         'taxedAmount' => "2808.8",
    //                         'exemptAmount' => "2191.2"
    //                     ]
    //                 ],
    //                 'otherPayments' => []
    //             ],
    //             'deductions' => [
    //                 [
    //                     'deductionTypeCode' => "001",
    //                     'code' => "00301",
    //                     'concept' => "Seguridad Social",
    //                     'amount' => "200"
    //                 ],
    //                 [
    //                     'deductionTypeCode' => "002",
    //                     'code' => "00302",
    //                     'concept' => "ISR",
    //                     'amount' => "100"
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
