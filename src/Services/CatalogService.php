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
}