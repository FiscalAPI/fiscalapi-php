<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

class ProductService extends AbstractService implements ProductServiceInterface
{
    /**
     * Constructor del servicio de productos
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'products');
    }

    /**
     * {@inheritdoc}
     */
    public function list(int $pageNumber = 1, int $pageSize = 50 ): FiscalApiHttpResponseInterface
    {
        $queryParams = [
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize
        ];

        $options = [
            'query_params' => $this->normalizeQueryParams($queryParams),
        ];

        return $this->httpClient->get($this->buildResourceUrl(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->get($this->buildResourceUrl($id));
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->post(
            $this->buildResourceUrl(),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->put($this->buildResourceUrl($data["id"]),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->delete($this->buildResourceUrl($id));
    }
}
