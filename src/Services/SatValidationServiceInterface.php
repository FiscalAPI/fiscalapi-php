<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Interfaz para el servicio de validaciones SAT.
 *
 * Expone el catálogo de tipos de validación, los estatus que cada tipo puede tomar
 * y la ejecución de validaciones sobre un CFDI o sobre un RFC.
 */
interface SatValidationServiceInterface
{
    /**
     * Recupera los tipos de validación disponibles.
     *
     * Devuelve en 'data' un arreglo de elementos con las claves 'id' y 'description',
     * en el orden del catálogo.
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function getTypes(): FiscalApiHttpResponseInterface;

    /**
     * Recupera un tipo de validación por su id.
     *
     * Devuelve en 'data' un elemento con las claves 'id' y 'description'.
     * Responde 404 si el tipo no existe o está inactivo.
     *
     * @param string $id Id del tipo de validación, por ejemplo 'sat.cfdi.status'.
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si el id está vacío.
     */
    public function getTypeById(string $id): FiscalApiHttpResponseInterface;

    /**
     * Recupera los estatus que un tipo de validación puede tomar al ejecutarse.
     *
     * Devuelve en 'data' un arreglo de elementos con las claves 'id' y 'description',
     * ordenados con los estatus aprobatorios primero. Es una consulta de catálogo:
     * no ejecuta la validación ni consume créditos. Responde 404 si el tipo no existe
     * o está inactivo.
     *
     * @param string $id Id del tipo de validación, por ejemplo 'sat.cfdi.status'.
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si el id está vacío.
     */
    public function getStatuses(string $id): FiscalApiHttpResponseInterface;

    /**
     * Ejecuta las validaciones solicitadas.
     *
     * Claves del arreglo:
     * - 'xml' (string, opcional): CFDI completo codificado en base64. Excluyente con 'tin'.
     *   Permite solicitar cualquier tipo de validación; las listas negras consultan el RFC del emisor.
     * - 'tin' (string, opcional): RFC. Excluyente con 'xml'. Solo permite solicitar listas negras
     *   (SatValidationTypeIds::BLACKLIST_69B y SatValidationTypeIds::BLACKLIST_69B_BIS).
     * - 'validationTypes' (string[], obligatorio): ids de los tipos a ejecutar, sin vacíos ni duplicados.
     *
     * Uno de 'xml' o 'tin' es obligatorio y no pueden enviarse ambos.
     *
     * Devuelve en 'data' un arreglo con un elemento por tipo ejecutado, en el orden del catálogo
     * y no en el solicitado. Cada elemento tiene las claves 'type' ('id', 'description'),
     * 'status' ('id', 'description', 'details') y 'passed'. La clave 'details' es texto libre
     * con los hechos del caso y puede ser null.
     *
     * Cada tipo solicitado consume un crédito de validación. El cobro es todo o nada y ocurre
     * antes de ejecutar: si el saldo no alcanza para todos, no se ejecuta ninguno y la API
     * responde 403. Un resultado adverso de negocio sigue respondiendo 200: el veredicto es
     * la clave 'passed' de cada elemento.
     *
     * @param array $data Datos de la solicitud ('xml' o 'tin', y 'validationTypes')
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si 'validationTypes' falta o está vacío.
     */
    public function validate(array $data): FiscalApiHttpResponseInterface;
}
