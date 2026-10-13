<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Models\WebhookEvent;
use Fiscalapi\Webhooks\Webhook;
use ReflectionMethod;

/**
 * Webhook::verify() contra un reloj fijo: llama al método privado Webhook::verifyAt(), que no es parte de la API pública,
 * para que el vector oficial (de 2021) y los límites de la tolerancia se prueben sin depender del reloj.
 */
final class FixedClockWebhook
{
    /**
     * @param array<string, mixed> $headers
     * @param mixed $secret
     */
    public static function verifyAt(array $headers, string $body, $secret, int $toleranceSeconds, int $now): WebhookEvent
    {
        $verifyAt = new ReflectionMethod(Webhook::class, 'verifyAt');
        $verifyAt->setAccessible(true);

        return $verifyAt->invoke(null, $headers, $body, $secret, $toleranceSeconds, $now);
    }

    /**
     * Contenedor de funciones: no se instancia.
     */
    private function __construct()
    {
    }
}
