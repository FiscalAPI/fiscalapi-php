<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de catálogos
 */
interface CatalogServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de catálogos
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 20): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un catálogo por su ID
     *
     * @param string $id Id del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id): FiscalApiHttpResponseInterface;

    /**
     * Crea un nuevo catálogo
     *
     * @param array $data Datos del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza un catálogo existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina un catálogo
     *
     * @param string $id Id del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}