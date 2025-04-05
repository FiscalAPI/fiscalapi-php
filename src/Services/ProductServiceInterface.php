<?php

declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalapiHttpResponseInterface;

interface ProductServiceInterface
{
    /**
     * Obtiene una lista de productos
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @param array $filters Filtros adicionales
     * @return FiscalapiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 20, array $filters = []): FiscalapiHttpResponseInterface;

    /**
     * Obtiene un producto por su ID
     *
     * @param string $id ID del producto
     * @return FiscalapiHttpResponseInterface
     */
    public function get(string $id): FiscalapiHttpResponseInterface;

    /**
     * Crea un nuevo producto
     *
     * @param array $data Datos del producto
     * @return FiscalapiHttpResponseInterface
     */
    public function create(array $data): FiscalapiHttpResponseInterface;

    /**
     * Actualiza un producto existente
     *
     * @param string $id ID del producto
     * @param array $data Datos a actualizar
     * @return FiscalapiHttpResponseInterface
     */
    public function update(string $id, array $data): FiscalapiHttpResponseInterface;

    /**
     * Elimina un producto
     *
     * @param string $id ID del producto
     * @return FiscalapiHttpResponseInterface
     */
    public function delete(string $id): FiscalapiHttpResponseInterface;
}