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

    // Aquí puedes añadir métodos específicos para productos si es necesario
}