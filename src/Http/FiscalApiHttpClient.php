<?php
declare(strict_types=1);

namespace Fiscalapi\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;


class FiscalApiHttpClient implements FiscalApiHttpClientInterface
{
    private Client $client;
    private FiscalApiSettings $settings;

    public function __construct(FiscalApiSettings $settings)
    {
        $this->settings = $settings;

        $stack = HandlerStack::create();

        $options = [
            'base_uri' => $settings->getBaseUrl(),
            'handler' => $stack,
            'headers' => [
                'X-API-KEY' => $settings->getApiKey(),
                'X-TENANT-KEY' => $settings->getTenant(),
                'X-TIME-ZONE' => $settings->getTimeZone(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'http_errors' => false,
            'verify' => $settings->isVerifySsl()
        ];

        $this->client = new Client($options);

        if ($settings->isDebug()) {
            echo "DEBUG CONFIG: Client inicializado con base_uri: {$settings->getBaseUrl()}\n";
        }
    }

    public function get(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    public function delete(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }

    public function patch(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    public function head(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }

    public function options(string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        return $this->request('OPTIONS', $uri, $options);
    }

    public function request(string $method, string $uri, array $options = []): FiscalApiHttpResponseInterface
    {
        $this->prepareRequestOptions($options);

        try {
            // Depurar la URL construida completa
            $baseUrl = $this->settings->getBaseUrl();
            $fullUrl = $uri;
            if (!preg_match('/^https?:\/\//', $uri)) {
                // Es una ruta relativa, construir la URL completa
                $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
                if ($this->settings->isDebug()) {
                    echo "DEBUG URL: Base URL: '{$baseUrl}', URI relativa: '{$uri}', URL completa: '{$fullUrl}'\n";
                }
                $uri = $fullUrl;
            }

            $this->logRequest($method, $uri, $options);
            $response = $this->client->request($method, $uri, $options);
            $this->logResponse($response);

            return new FiscalApiHttpResponse($response);
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(
                'Error en la petición HTTP: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function prepareRequestOptions(array &$options): void
    {
        // Convertir datos para el payload JSON si se proporcionan
        if (isset($options['data'])) {
            if (!is_array($options['data'])) {
                throw new \InvalidArgumentException('El parámetro "data" debe ser un array asociativo');
            }
            $options[RequestOptions::JSON] = $options['data'];
            unset($options['data']);
        }

        // Establecer parámetros de consulta si se proporcionan
        if (isset($options['query_params']) && is_array($options['query_params'])) {
            $options[RequestOptions::QUERY] = $options['query_params'];
            unset($options['query_params']);
        }

        // Añadir headers personalizados
        if (isset($options['headers']) && is_array($options['headers'])) {
            // Los headers se fusionan automáticamente con los predeterminados
        }

        // Permitir sobrescribir la configuración de SSL para solicitudes específicas
        if (!isset($options[RequestOptions::VERIFY])) {
            $options[RequestOptions::VERIFY] = $this->settings->isVerifySsl();
        }
    }

    private function logRequest(string $method, string $uri, array $options): void
    {
        if (!$this->settings->isDebug()) {
            return;
        }

        $logData = [
            'method' => $method,
            'uri' => $uri,
            'query_params' => $options[RequestOptions::QUERY] ?? [],
            'headers' => $options['headers'] ?? [],
            'verify_ssl' => $options[RequestOptions::VERIFY] ?? $this->settings->isVerifySsl()
        ];

        // Registrar el cuerpo de la solicitud si existe
        if (isset($options[RequestOptions::JSON])) {
            $logData['body'] = $options[RequestOptions::JSON];
        } elseif (isset($options[RequestOptions::BODY])) {
            $logData['body'] = $options[RequestOptions::BODY];
        }

        echo "REQUEST: " . json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

    }

    private function logResponse(ResponseInterface $response): void
    {
        if (!$this->settings->isDebug()) {
            return;
        }

        $body = (string) $response->getBody();
        $jsonBody = !empty($body) ? json_decode($body, true) : null;

        $logData = [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $jsonBody ?: $body
        ];

        echo "RESPONSE: " . json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        // Restaurar el puntero del cuerpo para que pueda leerse nuevamente
        $response->getBody()->rewind();
    }
}