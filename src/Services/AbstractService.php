<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Clase abstracta base para implementar servicios de FiscalAPI
 */
abstract class AbstractService implements FiscalApiServiceInterface
{
    protected FiscalApiHttpClientInterface $httpClient;
    protected string $resourcePath;

    /**
     * Constructor base para servicios
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     * @param string $resourcePath Ruta del recurso (ej: 'products')
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient, string $resourcePath)
    {
        $this->httpClient = $httpClient;
        $this->resourcePath = trim($resourcePath, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function list(int $pageNumber = 1, int $pageSize = 20): FiscalApiHttpResponseInterface
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
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException("El campo 'id' es obligatorio para actualizar un recurso");
        }

        return $this->httpClient->put(
            $this->buildResourceUrl($data['id']),
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

    /**
     * Construye la URL del recurso
     *
     * @param string|null $id ID del recurso (opcional)
     * @param string|null $subPath Subruta adicional (opcional)
     * @return string
     */
    protected function buildResourceUrl(?string $id = null, ?string $subPath = null): string
    {
        $url = '/' . $this->resourcePath;

        if ($id !== null) {
            $url .= '/' . urlencode($id);
        }

        if ($subPath !== null) {
            $url .= '/' . trim($subPath, '/');
        }

        return $url;
    }

    /**
     * Normaliza los parámetros de consulta
     *
     * @param array $params Parámetros
     * @return array
     */
    protected function normalizeQueryParams(array $params): array
    {
        return array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}