<?php
declare(strict_types=1);
namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiSettings;

class FiscalApiClient implements FiscalApiClientInterface
{
    private FiscalApiHttpClientInterface $httpClient;
    private ?ProductServiceInterface $productService = null;

    /**
     * Constructor del cliente principal de FiscalAPI.
     *
     * @param FiscalApiSettings $settings Configuración del cliente HTTP.
     */
    public function __construct(FiscalApiSettings $settings)
    {
        $this->httpClient = FiscalApiClientFactory::create($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductService(): ProductServiceInterface
    {
        if ($this->productService === null) {
            $this->productService = new ProductService($this->httpClient);
        }

        return $this->productService;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): FiscalApiHttpClientInterface
    {
        return $this->httpClient;
    }
}