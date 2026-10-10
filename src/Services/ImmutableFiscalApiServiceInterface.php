<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz base de un servicio cuyo recurso se consulta, se crea y se elimina, pero no se actualiza
 * (por ejemplo, los certificados). FiscalApiServiceInterface la extiende con update().
 */
interface ImmutableFiscalApiServiceInterface
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
     * Elimina un recurso
     *
     * @param string $id Id del recurso
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}