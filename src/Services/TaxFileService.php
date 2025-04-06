<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de certificados CSD del SAT
 */
class TaxFileService extends AbstractService implements TaxFileServiceInterface
{
    /**
     * Constructor del servicio de certificados CSD del SAT
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'tax-files');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultReferences(string $personId): FiscalApiHttpResponseInterface
    {
        $subPath = sprintf('%s/default-references', $personId);
        $url = $this->buildResourceUrl(null, $subPath);
        
        return $this->httpClient->get($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues(string $personId): FiscalApiHttpResponseInterface
    {
        $subPath = sprintf('%s/default-values', $personId);
        $url = $this->buildResourceUrl(null, $subPath);
        
        return $this->httpClient->get($url);
    }
}