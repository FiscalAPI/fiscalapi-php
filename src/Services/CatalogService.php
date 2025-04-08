<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de catálogos
 */
class CatalogService extends AbstractService implements CatalogServiceInterface
{
    /**
     * Constructor del servicio de catálogos
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'catalogs');
    }

    /**
     * Recupera un registro de un catálogo por catalogName y id.
     * 
     * @param string $catalogName Nombre del catálogo
     * @param string $id Id del registro en el catalogName
     * @return FiscalApiHttpResponseInterface
     */
    public function getById(string $catalogName, string $id): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/key/%s', $catalogName, $id);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

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
    ): FiscalApiHttpResponseInterface {
        $path = sprintf('%s/%s', $catalogName, $searchText);
        
        $queryParams = [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize
        ];

        $options = [
            'query_params' => $this->normalizeQueryParams($queryParams),
        ];

        return $this->httpClient->get($this->buildResourceUrl(null, $path), $options);
    }
}