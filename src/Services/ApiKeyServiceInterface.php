<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de API Keys
 */
interface ApiKeyServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de API Keys
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una API Key por su ID
     *
     * @param string $id Id de la API Key
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea una nueva API Key
     *
     * @param array $data Datos de la API Key
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza una API Key existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina una API Key
     *
     * @param string $id Id de la API Key
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}