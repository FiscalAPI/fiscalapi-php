<?php

declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de personas
 */
interface PersonServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de personas
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una persona por su ID
     *
     * @param string $id Id de la persona
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea una nueva persona
     *
     * @param array $data Datos de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza una persona existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina una persona
     *
     * @param string $id Id de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}