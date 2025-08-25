<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use BadMethodCallException;
use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de descarga masiva de catálogos
 */
class DownloadCatalogService extends AbstractService implements DownloadCatalogServiceInterface
{
    /**
     * Constructor del servicio de catálogos de descarga masiva.
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'download-catalogs');
    }

    /**
     * Recupera todos los nombres de los catálogos de descarga masiva disponibles para listarlos por nombre.
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function getList(): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->get($this->buildResourceUrl());
    }

    /**
     * Lista todos los registros de un catálogo pasando el nombre del catálogo.
     *
     * @param string $catalogName Nombre del catálogo
     * @return FiscalApiHttpResponseInterface
     */
    public function listCatalog(string $catalogName): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s', $catalogName);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * No se implementa la paginación. Utiliza getList y listCatalog en su lugar.
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     * @throws BadMethodCallException
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface
    {
        throw new BadMethodCallException('Utiliza getList y listCatalog en su lugar.');
    }

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param string $id Id del recurso
     * @param bool $details indica si debe recuperar los registros relacionados
     * @return FiscalApiHttpResponseInterface
     * @throws BadMethodCallException
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface
    {
        throw new BadMethodCallException('Utiliza getList y listCatalog en su lugar.');
    }

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param array $data Datos del recurso
     * @return FiscalApiHttpResponseInterface
     * @throws BadMethodCallException
     */
    public function create(array $data): FiscalApiHttpResponseInterface
    {
        throw new BadMethodCallException('Utiliza getList y listCatalog en su lugar.');
    }

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     * @throws BadMethodCallException
     */
    public function update(array $data): FiscalApiHttpResponseInterface
    {
        throw new BadMethodCallException('Utiliza getList y listCatalog en su lugar.');
    }

    /**
     * No se implementa. Utiliza getList y listCatalog en su lugar.
     *
     * @param string $id Id del recurso
     * @return FiscalApiHttpResponseInterface
     * @throws BadMethodCallException
     */
    public function delete(string $id): FiscalApiHttpResponseInterface
    {
        throw new BadMethodCallException('Utiliza getList y listCatalog en su lugar.');
    }
}