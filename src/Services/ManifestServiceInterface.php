<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Interfaz para el servicio de manifiestos.
 *
 * Expone la firma de la carta manifiesto del contribuyente con su e.firma (FIEL).
 */
interface ManifestServiceInterface
{
    /**
     * Firma la carta manifiesto del contribuyente.
     *
     * Requiere los archivos de la e.firma (FIEL), no los del CSD de timbrado. El RFC del
     * certificado debe corresponder a una persona registrada en el tenant; si no existe,
     * la API responde 404.
     *
     * Claves del arreglo de solicitud:
     * - 'base64Cer' (string, obligatorio): certificado .cer de la FIEL en base64. Máximo 24576 caracteres.
     * - 'base64Key' (string, obligatorio): llave privada .key de la FIEL en base64. Máximo 24576 caracteres.
     * - 'password' (string, obligatorio): contraseña de la llave privada. Máximo 256 caracteres.
     *
     * Devuelve en 'data' el PDF de la carta manifiesto ya firmada.
     *
     * @param array{
     *     base64Cer: string,
     *     base64Key: string,
     *     password: string
     * } $data Datos de la solicitud de firma
     * @return FiscalApiHttpResponseInterface Respuesta cuya clave 'data' contiene
     *     array{base64File: string, fileName: string, fileExtension: string}, donde 'base64File'
     *     es el PDF firmado en base64, 'fileName' es '<RFC>.pdf' y 'fileExtension' es '.pdf'.
     * @throws InvalidArgumentException Si falta 'base64Cer', 'base64Key' o 'password', o si vienen vacíos.
     */
    public function sign(array $data): FiscalApiHttpResponseInterface;
}
