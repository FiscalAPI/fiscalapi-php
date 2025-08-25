<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de descarga masiva de catálogos
 */
interface DownloadCatalogServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Recupera todos los nombres de los catálogos de descarga masiva disponibles para listarlos por nombre.
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function getList(): FiscalApiHttpResponseInterface;

    /**
     * Lista todos los registros de un catálogo pasando el nombre del catálogo.
     *
     * @param string $catalogName Nombre del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function listCatalog(string $catalogName): FiscalApiHttpResponseInterface;

    /**
     * No se implementa la paginación. Utiliza getList y listCatalog en su lugar.
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @throws \BadMethodCallException
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param string $id Id del recurso
     * @param bool $details indica si debe recuperar los registros relacionados
     * @throws \BadMethodCallException
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param array $data Datos del recurso
     * @throws \BadMethodCallException
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param array $data Datos a actualizar
     * @throws \BadMethodCallException
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param string $id Id del recurso
     * @throws \BadMethodCallException
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}