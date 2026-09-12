<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Identificadores de los tipos de validación SAT.
 *
 * Son los valores que se envían en la clave 'validationTypes' al ejecutar validaciones
 * y los que devuelve la API en 'type.id'.
 *
 * @see SatValidationStatusIds Identificadores de los estatus que puede tomar cada tipo.
 */
final class SatValidationTypeIds
{
    /**
     * Estructura del XML conforme al Anexo 20 y a los complementos declarados en schemaLocation.
     * Requiere la clave 'xml'.
     */
    public const XML_STRUCTURE = 'sat.xml.structure';

    /**
     * Vigencia del certificado del emisor en la fecha de emisión del CFDI.
     * Requiere la clave 'xml'.
     */
    public const CERTIFICATE_VALIDITY = 'sat.certificate.validity';

    /**
     * Sello del comprobante contra su cadena original y el certificado del emisor.
     * Requiere la clave 'xml'.
     */
    public const CFDI_SELLO = 'sat.cfdi.sello';

    /**
     * Sello del SAT en el complemento TimbreFiscalDigital.
     * Requiere la clave 'xml'.
     */
    public const TFD_SELLO = 'sat.tfd.sello';

    /**
     * Estado del comprobante en el servicio de consulta de CFDI del SAT.
     * Requiere la clave 'xml'.
     */
    public const CFDI_STATUS = 'sat.cfdi.status';

    /**
     * Situación del RFC en el listado del artículo 69-B del CFF.
     * Admite la clave 'xml' (usa el RFC del emisor) o la clave 'tin'.
     */
    public const BLACKLIST_69B = 'sat.blacklist.69b';

    /**
     * Situación del RFC en el listado del artículo 69-B Bis del CFF.
     * Admite la clave 'xml' (usa el RFC del emisor) o la clave 'tin'.
     */
    public const BLACKLIST_69B_BIS = 'sat.blacklist.69bbis';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
