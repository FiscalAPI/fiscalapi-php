<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Identificadores de los estatus que puede tomar una validación SAT al ejecutarse.
 *
 * Son los valores que devuelve la API en 'status.id'. Cada tipo de validación solo puede
 * tomar un subconjunto de estos estatus; el subconjunto exacto se consulta con
 * SatValidationServiceInterface::getStatuses().
 *
 * El veredicto no se deduce del estatus: cada resultado trae su propia clave 'passed'.
 *
 * @see SatValidationTypeIds Identificadores de los tipos de validación.
 */
final class SatValidationStatusIds
{
    /** Estructura, sello del CFDI o sello del TFD verificados correctamente. */
    public const VALIDO = 'Valido';

    /** Estructura, sello del CFDI o sello del TFD que no verifican. */
    public const INVALIDO = 'Invalido';

    /** Certificado vigente a la fecha de emisión, o comprobante vigente ante el SAT. */
    public const VIGENTE = 'Vigente';

    /** El certificado del emisor ya había caducado al firmar. */
    public const EXPIRADO = 'Expirado';

    /** El certificado del emisor aún no era válido al firmar. */
    public const NO_VIGENTE_AUN = 'NoVigenteAun';

    /** El comprobante está cancelado ante el SAT. */
    public const CANCELADO = 'Cancelado';

    /** El SAT no encuentra el comprobante con los datos consultados. Solo en sat.cfdi.status. */
    public const NO_ENCONTRADO = 'NoEncontrado';

    /** El RFC no figura en el listado. Solo en las listas negras. */
    public const NO_LISTADO = 'NoListado';

    /** El SAT notificó al emisor la presunción de operaciones inexistentes. Solo en sat.blacklist.69b. */
    public const PRESUNTO = 'Presunto';

    /** El emisor desvirtuó la presunción. Solo en sat.blacklist.69b. */
    public const DESVIRTUADO = 'Desvirtuado';

    /** El emisor fue publicado en el listado definitivo. */
    public const DEFINITIVO = 'Definitivo';

    /** Una resolución o sentencia firme dejó sin efectos el procedimiento. */
    public const SENTENCIA_FAVORABLE = 'SentenciaFavorable';

    /**
     * El servicio externo no respondió o no hay corte de listado importado.
     * Es un resultado de ejecución: responde 200, 'passed' es false y consume crédito.
     */
    public const NO_DISPONIBLE = 'NoDisponible';

    /**
     * No se evaluó porque la entrada no lo permite (XML inválido, sin TimbreFiscalDigital, sin certificado).
     * Es un resultado de ejecución: responde 200, 'passed' es false y consume crédito.
     */
    public const OMITIDO = 'Omitido';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
