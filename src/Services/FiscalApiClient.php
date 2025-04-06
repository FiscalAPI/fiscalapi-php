<?php
declare(strict_types=1);
namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiSettings;

class FiscalApiClient implements FiscalApiClientInterface
{
    private FiscalApiHttpClientInterface $httpClient;
    private ?ProductServiceInterface $productService = null;
    private ?PersonServiceInterface $personService = null;
    private ?ApiKeyServiceInterface $apiKeyService = null;
    private ?CatalogServiceInterface $catalogService = null;

    /**
     * Constructor del cliente principal de FiscalAPI.
     *
     * @param FiscalApiSettings $settings Configuración del cliente HTTP.
     */
    public function __construct(FiscalApiSettings $settings)
    {
        // Utiliza la fábrica para obtener un cliente HTTP
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
     * Obtiene el servicio de personas
     *
     * @return PersonServiceInterface
     */
    public function getPersonService(): PersonServiceInterface
    {
        if ($this->personService === null) {
            $this->personService = new PersonService($this->httpClient);
        }

        return $this->personService;
    }

    /**
     * Obtiene el servicio de API Keys
     *
     * @return ApiKeyServiceInterface
     */
    public function getApiKeyService(): ApiKeyServiceInterface
    {
        if ($this->apiKeyService === null) {
            $this->apiKeyService = new ApiKeyService($this->httpClient);
        }

        return $this->apiKeyService;
    }

    /**
     * Obtiene el servicio de catálogos
     *
     * @return CatalogServiceInterface
     */
    public function getCatalogService(): CatalogServiceInterface
    {
        if ($this->catalogService === null) {
            $this->catalogService = new CatalogService($this->httpClient);
        }

        return $this->catalogService;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): FiscalApiHttpClientInterface
    {
        return $this->httpClient;
    }
}