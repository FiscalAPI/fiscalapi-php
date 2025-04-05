<?php

declare(strict_types=1);

namespace Fiscalapi\Http;


use Psr\Http\Message\ResponseInterface;

class FiscalApiHttpResponse implements FiscalApiHttpResponseInterface
{
    private ResponseInterface $response;
    private ?array $decodedJson = null;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    public function getBody(): string
    {
        return (string) $this->response->getBody();
    }

    public function getJson(): array
    {
        if ($this->decodedJson === null) {
            $body = $this->getBody();
            $this->decodedJson = !empty($body) ? json_decode($body, true) : [];

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException(
                    'Error al decodificar la respuesta JSON: ' . json_last_error_msg()
                );
            }

            if (!is_array($this->decodedJson)) {
                $this->decodedJson = [];
            }
        }

        return $this->decodedJson;
    }

    public function getHeader(string $header): ?string
    {
        if (!$this->response->hasHeader($header)) {
            return null;
        }

        $headerValues = $this->response->getHeader($header);
        return $headerValues[0] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function getPsrResponse(): ResponseInterface
    {
        return $this->response;
    }
}