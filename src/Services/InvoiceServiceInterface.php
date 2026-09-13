<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de facturas
 */
interface InvoiceServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Crea una nueva factura: ingreso (I), egreso/nota de crédito (E), traslado (T), pago (P) o nómina (N).
     * Acepta complementos como Carta Porte, Pagos, Nómina, Impuestos Locales y Comercio Exterior
     * bajo la propiedad 'complement'.
     *
     * Escala decimal: el SAT valida la cantidad de decimales de varios campos y json_encode descarta los
     * ceros finales de un float (0.160000 se transmite como 0.16, lo que provoca CFDI40179 en
     * cfdi:TasaOCuota y CCE122 en cce20:TotalUSD). Para conservar la escala, esos campos se envían como
     * string con los decimales literales: 'taxRate' => '0.160000', 'tipoCambioUSD' => '16.9722',
     * 'cantidadAduana' => '1.000', 'valorUnitarioAduana' => '120.00', 'valorDolares' => '120.00'.
     * La API acepta número o string en los campos decimales.
     *
     * Receptor extranjero: 'recipient.countryId' (clave de 3 caracteres del catálogo c_Pais, viaja a
     * cfdi:Receptor@ResidenciaFiscal) es obligatorio cuando se envía 'recipient.foreignTin' (1 a 40
     * caracteres, viaja a cfdi:Receptor@NumRegIdTrib), y debe omitirse cuando el receptor trae un 'tin'
     * distinto de 'XEXX010101000' y la factura no lleva complemento de Comercio Exterior. Ambas reglas
     * se omiten cuando el receptor se envía por 'id'.
     *
     * Forma de $data['complement']['comercioExterior'] (complemento Comercio Exterior 2.0). Las claves
     * marcadas con '?' son opcionales y el tipo '?string' admite null. 'totalUSD' no se envía: la API lo
     * calcula como la suma de 'mercancias[].valorDolares'. La clave es 'tipoCambioUSD', con USD en
     * mayúsculas.
     *
     * array{
     *     motivoTrasladoId?: ?string,                  // c_MotivoTraslado. Aplica a traslados (typeCode 'T')
     *     claveDePedimentoId: string,                  // obligatorio. c_ClavePedimento, por ejemplo 'A1'
     *     certificadoOrigen: int,                      // obligatorio. 0 (sin certificado) o 1 (con certificado)
     *     numCertificadoOrigen?: ?string,              // 6 a 40 caracteres
     *     numeroExportadorConfiable?: ?string,         // hasta 50 caracteres
     *     incotermId?: ?string,                        // c_INCOTERM, por ejemplo 'FOB'
     *     observaciones?: ?string,                     // hasta 300 caracteres
     *     tipoCambioUSD: string|float,                 // obligatorio. Mayor a cero, 4 decimales
     *     emisor?: ?array{
     *         curp?: ?string,                          // 18 caracteres
     *         domicilio: array{                        // obligatorio cuando se envía 'emisor'
     *             calle: string,                       // obligatorio, hasta 100 caracteres
     *             numeroExterior?: ?string,            // hasta 55 caracteres
     *             numeroInterior?: ?string,            // hasta 55 caracteres
     *             coloniaId?: ?string,                 // clave de colonia
     *             localidadId?: ?string,               // clave de localidad
     *             referencia?: ?string,                // hasta 250 caracteres
     *             municipioId?: ?string,               // clave de municipio
     *             estadoId: string,                    // obligatorio. Clave de estado
     *             paisId: string,                      // obligatorio. c_Pais, normalmente 'MEX'
     *             codigoPostalId: string               // obligatorio. Clave de código postal
     *         }
     *     },
     *     receptor?: ?array{
     *         numRegIdTrib?: ?string,                  // 6 a 40 caracteres
     *         domicilio?: ?array{
     *             calle: string,                       // obligatorio, hasta 100 caracteres
     *             numeroExterior?: ?string,            // hasta 55 caracteres
     *             numeroInterior?: ?string,            // hasta 55 caracteres
     *             colonia?: ?string,                   // texto libre, hasta 120 caracteres
     *             localidad?: ?string,                 // texto libre, hasta 120 caracteres
     *             referencia?: ?string,                // hasta 250 caracteres
     *             municipio?: ?string,                 // texto libre, hasta 120 caracteres
     *             estado: string,                      // obligatorio, hasta 30 caracteres
     *             paisId: string,                      // obligatorio. c_Pais, por ejemplo 'USA'
     *             codigoPostal: string                 // obligatorio, hasta 12 caracteres
     *         }
     *     },
     *     propietarios?: ?array<array{
     *         numRegIdTrib: string,                    // obligatorio, 6 a 40 caracteres
     *         residenciaFiscalId: string               // obligatorio. c_Pais
     *     }>,
     *     destinatarios?: ?array<array{
     *         numRegIdTrib?: ?string,                  // 6 a 40 caracteres
     *         nombre?: ?string,                        // hasta 300 caracteres
     *         domicilios: array<array{                 // obligatorio, al menos un domicilio
     *             calle: string,                       // obligatorio, hasta 100 caracteres
     *             numeroExterior?: ?string,            // hasta 55 caracteres
     *             numeroInterior?: ?string,            // hasta 55 caracteres
     *             colonia?: ?string,                   // texto libre, hasta 120 caracteres
     *             localidad?: ?string,                 // texto libre, hasta 120 caracteres
     *             referencia?: ?string,                // hasta 250 caracteres
     *             municipio?: ?string,                 // texto libre, hasta 120 caracteres
     *             estado: string,                      // obligatorio, hasta 30 caracteres
     *             paisId: string,                      // obligatorio. c_Pais
     *             codigoPostal: string                 // obligatorio, hasta 12 caracteres
     *         }>
     *     }>,
     *     mercancias: array<array{                     // obligatorio, al menos una mercancía
     *         noIdentificacion: string,                // obligatorio, hasta 100 caracteres. Coincide con items[].itemSku
     *         fraccionArancelariaId?: ?string,         // c_FraccionArancelaria
     *         cantidadAduana?: string|float|null,      // mayor o igual a 0.001, 3 decimales
     *         unidadAduanaId?: ?string,                // c_UnidadAduana
     *         valorUnitarioAduana?: string|float|null, // mayor o igual a cero, 2 decimales
     *         valorDolares: string|float,              // obligatorio. Mayor o igual a cero, 2 decimales
     *         descripcionesEspecificas?: ?array<array{
     *             marca: string,                       // obligatorio, hasta 35 caracteres
     *             modelo?: ?string,                    // hasta 80 caracteres
     *             subModelo?: ?string,                 // hasta 50 caracteres
     *             numeroSerie?: ?string                // hasta 40 caracteres
     *         }>
     *     }>
     * }
     *
     * El domicilio del emisor usa claves catalogadas con sufijo 'Id' ('coloniaId', 'localidadId',
     * 'municipioId', 'estadoId', 'codigoPostalId') porque es un domicilio en México. Los domicilios del
     * receptor y de los destinatarios usan las claves de texto libre equivalentes sin sufijo ('colonia',
     * 'localidad', 'municipio', 'estado', 'codigoPostal'), y solo 'paisId' conserva el sufijo. La
     * asimetría es intencional: reutilizar una sola forma para los tres domicilios produce un complemento
     * inválido.
     *
     * @param array $data Datos de la factura
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Cancela una factura
     * 
     * @param array $data Solicitud para cancelar factura
     * @return FiscalApiHttpResponseInterface
     */
    public function cancel(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el PDF de una factura
     * 
     * @param array $data Solicitud para crear PDF
     * @return FiscalApiHttpResponseInterface
     */
    public function getPdf(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el XML de una factura
     * 
     * @param string $id Id de la factura
     * @return FiscalApiHttpResponseInterface
     */
    public function getXml(string $id): FiscalApiHttpResponseInterface;

    /**
     * Envía una factura por correo electrónico
     * 
     * @param array $data Solicitud para enviar factura
     * @return FiscalApiHttpResponseInterface
     */
    public function send(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el estado de una factura
     * 
     * @param array $data Solicitud para consultar estado
     * @return FiscalApiHttpResponseInterface
     */
    public function getStatus(array $data): FiscalApiHttpResponseInterface;
}