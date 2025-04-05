<?php
declare(strict_types=1);

namespace Fiscalapi\Services;


use Fiscalapi\Http\FiscalApiHttpClientInterface;

abstract class AbstractService
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
     * Construye la URL del recurso | buildEndpoint
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