<?php
declare(strict_types=1);

namespace Fiscalapi\Http;

use Psr\Http\Message\ResponseInterface;

interface FiscalApiHttpResponseInterface
{
    /**
     * Obtiene el código de estado HTTP
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Verifica si la respuesta fue exitosa (código 2xx)
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Obtiene el cuerpo de la respuesta como string
     *
     * @return string
     */
    public function getBody(): string;

    /**
     * Obtiene el cuerpo de la respuesta como array asociativo (decodificado de JSON)
     *
     * @return array
     */
    public function getJson(): array;

    /**
     * Obtiene un header específico de la respuesta
     *
     * @param string $header Nombre del header
     * @return string|null
     */
    public function getHeader(string $header): ?string;

    /**
     * Obtiene todos los headers de la respuesta
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Obtiene la respuesta PSR-7 subyacente
     *
     * @return ResponseInterface
     */
    public function getPsrResponse(): ResponseInterface;
}