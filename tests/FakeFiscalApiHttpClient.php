<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponse;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Cliente HTTP simulado: responde a cualquier petición con el cuerpo configurado y registra las URIs
 * pedidas. Los servicios del SDK lo reciben en lugar de FiscalApiHttpClient, así que cada prueba
 * recorre el camino real (servicio -> FiscalApiHttpResponse::getJson()) sin red.
 */
final class FakeFiscalApiHttpClient implements FiscalApiHttpClientInterface
{
    private string $body = '';

    /** @var string[] */
    private array $requestedUris = [];

    /** @var array<int, array{method: string, uri: string, options: array}> */
    private array $requests = [];

    public static function readFixture(string $name): string
    {
        $contents = file_get_contents(__DIR__ . '/fixtures/' . $name);
        if ($contents === false) {
            throw new \RuntimeException('No se pudo leer la fixture ' . $name);
        }

        return $contents;
    }

    public function respondWith(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string[]
     */
    public function getRequestedUris(): array
    {
        return $this->requestedUris;
    }

    /**
     * Peticiones recibidas: método, URI y las opciones tal como las armó el servicio (el cuerpo va en 'data').
     *
     * @return array<int, array{method: string, uri: string, options: array}>
     */
    public function getRequests(): array
    {
        return $this->requests;
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
        $this->requestedUris[] = $method . ' ' . $uri;
        $this->requests[] = ['method' => $method, 'uri' => $uri, 'options' => $options];

        return new FiscalApiHttpResponse(new Response(200, ['Content-Type' => 'application/json'], $this->body));
    }
}
