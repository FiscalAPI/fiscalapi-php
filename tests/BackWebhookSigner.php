<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

/**
 * La firma de WebhookSigner de fapi-back, copiada sin depender del back: HMAC-SHA256 sobre {id}.{timestamp}.{body} con los
 * bytes base64 del secreto (sin whsec_), una firma "v1,<base64>" por secreto unidas por un espacio, la vigente primero.
 * Firma los bytes del cuerpo tal cual: así una prueba puede firmar un cuerpo que no es UTF-8 válido.
 */
final class BackWebhookSigner
{
    private const SECRET_PREFIX = 'whsec_';

    /**
     * Un secreto como WebhookSecret.Generate del back: whsec_ + 32 bytes aleatorios en base64.
     */
    public static function generateSecret(): string
    {
        return self::SECRET_PREFIX . base64_encode(random_bytes(32));
    }

    public static function sign(string $id, int $timestamp, string $body, string ...$secrets): string
    {
        $content = $id . '.' . $timestamp . '.' . $body;

        return implode(' ', array_map(
            static function (string $secret) use ($content): string {
                $key = (string) base64_decode(substr($secret, strlen(self::SECRET_PREFIX)), true);

                return 'v1,' . base64_encode(hash_hmac('sha256', $content, $key, true));
            },
            $secrets
        ));
    }

    /**
     * @return array<string, string>
     */
    public static function headers(string $id, int $timestamp, string $body, string ...$secrets): array
    {
        return [
            'webhook-id' => $id,
            'webhook-timestamp' => (string) $timestamp,
            'webhook-signature' => self::sign($id, $timestamp, $body, ...$secrets),
        ];
    }

    /**
     * Contenedor de funciones: no se instancia.
     */
    private function __construct()
    {
    }
}
