<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de productos
 */
interface ProductServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de productos
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Lista los productos del tenant con los filtros asignados. GET /api/v4/products
     *
     * Sin filtros, la lista de siempre (primera página). list($pageNumber, $pageSize) sigue igual.
     *
     * @param array $filters 'search' (2 a 100 caracteres, sin distinguir mayúsculas: descripción o clave de producto o
     *                       servicio del SAT), 'pageNumber' (desde 1) y 'pageSize' (1 a 50). Cada clave viaja tal cual
     *                       en la query string; los valores null no se envían.
     * @return FiscalApiHttpResponseInterface Página de productos
     */
    public function getList(array $filters = []): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un producto por su ID
     *
     * @param string $id Id del producto
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea un nuevo producto
     *
     * @param array $data Datos del producto
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza un producto existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina un producto
     *
     * @param string $id Id del producto
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}