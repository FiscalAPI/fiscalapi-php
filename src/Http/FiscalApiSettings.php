<?php
declare(strict_types=1);

namespace Fiscalapi\Http;

class FiscalApiSettings
{
    private string $apiUrl;
    private string $apiKey;
    private string $apiVersion;
    private string $tenant;
    private string $timeZone;
    private bool $debug;
    private bool $verifySsl;

    public function __construct(
        string $apiUrl,
        string $apiKey,
        string $tenant,
        bool $debug = false,
        bool $verifySsl = true,
        string $apiVersion = 'v4',
        string $timeZone = 'America/Mexico_City'

    ) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;
        $this->tenant = $tenant;
        $this->timeZone = $timeZone;
        $this->debug = $debug;
        // Si estamos en modo debug, por defecto desactivamos la verificación SSL a menos que se especifique explícitamente
        $this->verifySsl = $debug ? false : $verifySsl;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function isVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function getBaseUrl(): string
    {
        return sprintf('%s/api/%s', $this->apiUrl, $this->apiVersion);
    }
}