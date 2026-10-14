<?php
declare(strict_types=1);

namespace Fiscalapi\Webhooks;

use InvalidArgumentException;

/**
 * La llave HMAC de un secreto de webhook. Los bytes viven solo aquí, nunca como argumento de otra función: así no llegan
 * a la traza de una excepción (que un logger como Sentry, Flare o el de Laravel enviaría) ni a un var_dump o print_r, que
 * muestran __debugInfo(). No tiene __toString(): convertirla a texto es un error.
 *
 * @internal Úsela a través de Webhook::verify().
 */
final class WebhookSigningKey
{
    private const SECRET_PREFIX = 'whsec_';
    private const REDACTED = '[redactado]';

    private string $bytes;

    private function __construct(string $bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * Decodifica el secreto ('whsec_' seguido de base64, o solo el base64). Los mensajes nombran el secreto por
     * $description (y su posición en una lista) y nunca incluyen su valor: terminarían en los logs del receptor.
     *
     * @param mixed $secret
     * @throws InvalidArgumentException El secreto es null, no es texto, no es base64 canónico o está vacío.
     */
    public static function fromSecret(
        #[\SensitiveParameter]
        $secret,
        string $description
    ): self {
        if ($secret === null) {
            throw new InvalidArgumentException("{$description} es null.");
        }
        if (!is_string($secret)) {
            throw new InvalidArgumentException(
                "{$description} no es texto: pase el secreto (whsec_…) o una lista de secretos.");
        }

        $encoded = strpos($secret, self::SECRET_PREFIX) === 0 ? substr($secret, strlen(self::SECRET_PREFIX)) : $secret;
        $bytes = base64_decode($encoded, true);
        // base64_decode(…, true) acepta un secreto sin relleno y salta espacios y saltos de línea: un secreto truncado o
        // mal copiado se rechaza aquí en vez de verificar con otra llave.
        if ($bytes === false || base64_encode($bytes) !== $encoded) {
            throw new InvalidArgumentException(
                "{$description} no es whsec_ seguido de base64: cópielo completo del webhook.");
        }
        if ($bytes === '') {
            throw new InvalidArgumentException("{$description} está vacío.");
        }

        return new self($bytes);
    }

    /**
     * La firma v1 (HMAC-SHA256 en base64) de $content.
     */
    public function sign(string $content): string
    {
        return base64_encode(hash_hmac('sha256', $content, $this->bytes, true));
    }

    /**
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['bytes' => self::REDACTED];
    }
}
