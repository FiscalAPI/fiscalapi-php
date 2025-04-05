<?php

declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

interface ProductServiceInterface
{
    /**
     * Obtiene una lista de productos
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 20): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un producto por su ID
     *
     * @param string $id Id del producto
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id): FiscalApiHttpResponseInterface;

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