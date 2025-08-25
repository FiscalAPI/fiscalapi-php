<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de reglas de descarga
 */
class DownloadRuleService extends AbstractService implements DownloadRuleServiceInterface
{
    /**
     * Constructor del servicio de reglas de descarga
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'download-rules');
    }

    /**
     * Crea una regla de descarga de prueba
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function createTestRule(): FiscalApiHttpResponseInterface
    {
        $path = 'test';
        return $this->httpClient->post($this->buildResourceUrl($path));
    }
}