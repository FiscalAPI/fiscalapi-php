<?php
declare(strict_types=1);

namespace Fiscalapi\Http;

interface FiscalApiHttpClientInterface
{
    /**
     * Realiza una petición HTTP GET
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP POST
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function post(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP PUT
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function put(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP DELETE
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP PATCH
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function patch(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP HEAD
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function head(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP OPTIONS
     *
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function options(string $uri, array $options = []): FiscalApiHttpResponseInterface;

    /**
     * Realiza una petición HTTP genérica
     *
     * @param string $method Método HTTP
     * @param string $uri URI relativa o absoluta
     * @param array $options Opciones de la petición
     * @return FiscalApiHttpResponseInterface
     */
    public function request(string $method, string $uri, array $options = []): FiscalApiHttpResponseInterface;
}