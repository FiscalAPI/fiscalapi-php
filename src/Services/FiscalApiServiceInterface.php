<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz base para todos los servicios de FiscalAPI
 */
interface FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de recursos
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un recurso por su ID
     *
     * @param string $id Id del recurso
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea un nuevo recurso
     *
     * @param array $data Datos del recurso
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza un recurso existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina un recurso
     *
     * @param string $id Id del recurso
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}