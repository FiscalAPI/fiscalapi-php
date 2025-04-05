<?php


declare(strict_types=1);

namespace Fiscalapi;

use Fiscalapi\Services\Http\FiscalapiHttpClientInterface;
use Fiscalapi\Services\Http\FiscalapiHttpClient;
use Fiscalapi\Services\Http\FiscalapiSettings;
use Fiscalapi\Services\ProductService;
use Fiscalapi\Services\ProductServiceInterface;

class FiscalapiClient implements FiscalapiClientInterface
{
    private FiscalapiHttpClientInterface $httpClient;
    private ?ProductServiceInterface $productService = null;

    /**
     * Constructor del cliente principal de FiscalAPI
     *
     * @param FiscalapiSettings|FiscalapiHttpClientInterface $clientOrSettings Configuración o cliente HTTP
     */
    public function __construct($clientOrSettings)
    {
        if ($clientOrSettings instanceof FiscalapiHttpClientInterface) {
            $this->httpClient = $clientOrSettings;
        } elseif ($clientOrSettings instanceof FiscalapiSettings) {
            $this->httpClient = new FiscalapiHttpClient($clientOrSettings);
        } else {
            throw new \InvalidArgumentException(
                'El parámetro debe ser una instancia de FiscalapiSettings o FiscalapiHttpClientInterface'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function products(): ProductServiceInterface
    {
        if ($this->productService === null) {
            $this->productService = new ProductService($this->httpClient);
        }

        return $this->productService;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): FiscalapiHttpClientInterface
    {
        return $this->httpClient;
    }
}