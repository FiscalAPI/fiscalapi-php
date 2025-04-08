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
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un catálogo por su ID
     *
     * @param string $id Id del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

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

    /**
     * Recupera un registro de un catálogo por catalogName y id.
     * 
     * @param string $catalogName Nombre del catálogo
     * @param string $id Id del registro en el catalogName
     * @return FiscalApiHttpResponseInterface
     */
    public function getById(string $catalogName, string $id): FiscalApiHttpResponseInterface;

    /**
     * Busca en un catálogo.
     * 
     * @param string $catalogName Nombre del catálogo. Debe ser un catálogo recuperado de list()
     * @param string $searchText Criterio de búsqueda. Debe tener 4 caracteres de longitud como mínimo.
     * @param int $pageNumber Número de página a recuperar (default: 1)
     * @param int $pageSize Tamaño de la página entre 1 y 100 registros por página (default: 50)
     * @return FiscalApiHttpResponseInterface
     */
    public function search(
        string $catalogName, 
        string $searchText, 
        int $pageNumber = 1, 
        int $pageSize = 50
    ): FiscalApiHttpResponseInterface;
}