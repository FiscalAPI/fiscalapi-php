<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClient;
use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiSettings;

/**
 * Fábrica para crear clientes HTTP para FiscalAPI
 */
class FiscalApiClientFactory
{
    /**
     * Almacena instancias cacheadas de clientes HTTP
     *
     * @var array<string, FiscalApiHttpClientInterface>
     */
    private static array $clients = [];

    /**
     * Crea un nuevo cliente HTTP para FiscalAPI
     *
     * @param FiscalApiSettings $settings Configuración de FiscalAPI
     * @return FiscalApiHttpClientInterface Instancia del cliente HTTP
     * @throws \InvalidArgumentException Si la configuración es nula o inválida
     */
    public static function create(FiscalApiSettings $settings): FiscalApiHttpClientInterface
    {
        // Crea una clave única para cachear el cliente
        $clientKey = sprintf(
            '%s:%s:%s',
            $settings->getApiKey(),
            $settings->getTenant(),
            $settings->getApiUrl()
        );

        // Devuelve el cliente cacheado si existe
        if (isset(self::$clients[$clientKey])) {
            return self::$clients[$clientKey];
        }

        // Crea y cachea el cliente
        $client = new FiscalApiHttpClient($settings);
        self::$clients[$clientKey] = $client;

        return $client;
    }

    /**
     * Destruye todas las instancias cacheadas de clientes HTTP
     * Útil para pruebas unitarias o al finalizar la aplicación
     *
     * @return void
     */
    public static function clearAll(): void
    {
        self::$clients = [];
    }

    /**
     * Elimina una instancia específica del cliente HTTP del caché
     *
     * @param FiscalApiSettings $settings Configuración de FiscalAPI
     * @return bool true si se eliminó el cliente, false si no existía
     */
    public static function clear(FiscalApiSettings $settings): bool
    {
        $clientKey = sprintf(
            '%s:%s:%s',
            $settings->getApiKey(),
            $settings->getTenant(),
            $settings->getApiUrl()
        );

        if (isset(self::$clients[$clientKey])) {
            unset(self::$clients[$clientKey]);
            return true;
        }

        return false;
    }
}