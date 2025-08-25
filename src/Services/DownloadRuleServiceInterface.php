<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de reglas de descarga
 */
interface DownloadRuleServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Crea una regla de descarga de prueba
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function createTestRule(): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una lista de reglas de descarga
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una regla de descarga por su ID
     *
     * @param string $id Id de la regla de descarga
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea una nueva regla de descarga
     *
     * @param array $data Datos de la regla de descarga
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza una regla de descarga existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina una regla de descarga
     *
     * @param string $id Id de la regla de descarga
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}