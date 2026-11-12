<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de la persona de la credencial.
 */
class MeService implements MeServiceInterface
{
    private const RESOURCE_PATH = '/me';

    private FiscalApiHttpClientInterface $httpClient;

    /**
     * Constructor del servicio de la persona de la credencial
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->get(self::RESOURCE_PATH);
    }
}
