<?php
declare(strict_types=1);

namespace Fiscalapi\Webhooks;

use Fiscalapi\Models\WebhookEvent;
use InvalidArgumentException;

/**
 * Verificación de las entregas de webhooks de FiscalAPI, que se firman con Standard Webhooks
 * (https://www.standardwebhooks.com): HMAC-SHA256 sobre 'webhook-id.webhook-timestamp.cuerpo' con el secreto 'whsec_…'
 * del endpoint, en el header webhook-signature como 'v1,<base64>'. La firma es la única prueba de que una entrega viene
 * de FiscalAPI.
 */
final class Webhook
{
    /**
     * Diferencia máxima, en segundos (5 minutos), entre webhook-timestamp y el reloj del receptor, hacia atrás o hacia
     * adelante: una entrega capturada no se puede repetir después.
     */
    public const DEFAULT_TOLERANCE_SECONDS = 300;

    private const ID_HEADER = 'webhook-id';
    private const TIMESTAMP_HEADER = 'webhook-timestamp';
    private const SIGNATURE_HEADER = 'webhook-signature';
    private const SIGNATURE_VERSION = 'v1';
    private const SIGNATURE_SEPARATOR = ' ';
    private const VERSION_SEPARATOR = ',';
    private const SECRET_PREFIX = 'whsec_';

    /**
     * Verifica una entrega y devuelve su evento.
     *
     * Responda 2xx rápido y procese el evento después (por ejemplo en una cola): FiscalAPI corta cada intento a los 15 s
     * y lo reintenta. La entrega es al menos una vez: descarte las repetidas por getId().
     *
     * @param array<string, string|string[]> $headers Los headers de la petición; los nombres se comparan sin distinguir
     *     mayúsculas y un valor puede ser texto o lista de textos: sirven getallheaders(), getHeaders() de PSR-7 y
     *     $request->headers->all() de Symfony o Laravel. Una lista se une con ', ', como getHeaderLine() de PSR-7.
     * @param string $body El cuerpo CRUDO tal como llegó, antes de decodificarlo: file_get_contents('php://input'),
     *     getBody() de PSR-7 o $request->getContent() de Symfony o Laravel. Un cuerpo decodificado y vuelto a codificar
     *     cambia sus bytes (espacios, orden, escapes) y su firma deja de coincidir.
     * @param string|string[] $secret El secreto del endpoint ('whsec_…'; se acepta también sin el prefijo), o una lista de
     *     secretos durante una rotación (FiscalAPI firma con el nuevo y el anterior durante 24 h): basta que una firma v1
     *     del header coincida con uno de ellos.
     * @param int $toleranceSeconds Tolerancia del timestamp en segundos; por omisión DEFAULT_TOLERANCE_SECONDS.
     * @return WebhookEvent El evento del cuerpo verificado.
     * @throws WebhookVerificationException Falta un header, el timestamp no es válido o está fuera de la tolerancia,
     *     ninguna firma v1 coincide, el cuerpo no es UTF-8 válido o el cuerpo firmado no es un evento: rechace la entrega
     *     (por ejemplo con un 400) sin procesarla.
     * @throws InvalidArgumentException El secreto falta, no es texto o no es base64, la lista de secretos está vacía, la
     *     tolerancia es negativa o un header no es texto: es un error de configuración del receptor, no de la entrega. El
     *     mensaje nunca incluye el valor del secreto.
     */
    public static function verify(
        array $headers,
        string $body,
        $secret,
        int $toleranceSeconds = self::DEFAULT_TOLERANCE_SECONDS
    ): WebhookEvent {
        return self::verifyAt($headers, $body, $secret, $toleranceSeconds, time());
    }

    /**
     * La verificación contra el momento $now (segundos Unix) del receptor, para fijar el reloj en las pruebas.
     *
     * @internal Use verify().
     * @param array<string, string|string[]> $headers
     * @param string|string[] $secret
     */
    public static function verifyAt(array $headers, string $body, $secret, int $toleranceSeconds, int $now): WebhookEvent
    {
        $keys = self::decodeSecrets($secret);
        if ($toleranceSeconds < 0) {
            throw new InvalidArgumentException('La tolerancia no puede ser negativa.');
        }

        $id = self::requiredHeader($headers, self::ID_HEADER);
        $timestamp = self::requiredHeader($headers, self::TIMESTAMP_HEADER);
        $signatures = self::requiredHeader($headers, self::SIGNATURE_HEADER);

        self::ensureWithinTolerance($timestamp, $toleranceSeconds, $now);
        self::ensureSigned($keys, self::signedContent($id, $timestamp, $body), $signatures);

        return WebhookEnvelope::read($body);
    }

    /**
     * @param mixed $secret
     * @return string[] Las llaves HMAC.
     */
    private static function decodeSecrets($secret): array
    {
        if (!is_array($secret)) {
            return [self::decodeSecret($secret, 'El secreto de firma')];
        }

        if ($secret === []) {
            throw new InvalidArgumentException('Se necesita al menos un secreto de firma (whsec_…).');
        }

        $keys = [];
        foreach (array_values($secret) as $position => $each) {
            $keys[] = self::decodeSecret($each, "El secreto de firma en la posición {$position}");
        }

        return $keys;
    }

    /**
     * Los mensajes nombran el secreto por su posición y nunca incluyen su valor: terminarían en los logs del receptor.
     *
     * @param mixed $secret
     */
    private static function decodeSecret($secret, string $description): string
    {
        if ($secret === null) {
            throw new InvalidArgumentException("{$description} es null.");
        }
        if (!is_string($secret)) {
            throw new InvalidArgumentException(
                "{$description} no es texto: pase el secreto (whsec_…) o una lista de secretos.");
        }

        $encoded = strpos($secret, self::SECRET_PREFIX) === 0 ? substr($secret, strlen(self::SECRET_PREFIX)) : $secret;
        $key = base64_decode($encoded, true);
        if ($key === false) {
            throw new InvalidArgumentException(
                "{$description} no es whsec_ seguido de base64: cópielo completo del endpoint de webhooks.");
        }
        if ($key === '') {
            throw new InvalidArgumentException("{$description} está vacío.");
        }

        return $key;
    }

    /**
     * @param array<string, mixed> $headers
     */
    private static function requiredHeader(array $headers, string $name): string
    {
        foreach ($headers as $headerName => $value) {
            if (strtolower((string) $headerName) === $name) {
                $text = self::headerText($value, $name);
                if ($text === '') {
                    break;
                }

                return $text;
            }
        }

        throw new WebhookVerificationException("Falta el header {$name}.");
    }

    /**
     * @param mixed $value
     */
    private static function headerText($value, string $name): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value) && array_filter($value, 'is_string') === $value) {
            return implode(', ', $value);
        }

        throw new InvalidArgumentException("El header {$name} debe ser texto o una lista de textos.");
    }

    private static function ensureWithinTolerance(string $timestamp, int $toleranceSeconds, int $now): void
    {
        // Entero a entero, sin pasar por fechas: un valor enorme se rechaza por la tolerancia. $now y $seconds no son
        // negativos, así que la resta no se desborda a float.
        if (abs($now - self::unixSeconds($timestamp)) > $toleranceSeconds) {
            throw new WebhookVerificationException(
                'El header ' . self::TIMESTAMP_HEADER . " difiere más de {$toleranceSeconds} s del reloj del receptor: "
                . 'puede ser una entrega repetida o un reloj desajustado.');
        }
    }

    // Solo dígitos ('\z' y no '$', que aceptaría un salto de línea final) y dentro de PHP_INT_MAX: un signo, espacios,
    // decimales o un valor que (int) convertiría con pérdida no son segundos Unix.
    private static function unixSeconds(string $timestamp): int
    {
        $digits = ltrim($timestamp, '0');
        $max = (string) PHP_INT_MAX;
        $fits = strlen($digits) < strlen($max) || (strlen($digits) === strlen($max) && strcmp($digits, $max) <= 0);
        if (preg_match('/\A[0-9]+\z/', $timestamp) !== 1 || !$fits) {
            throw new WebhookVerificationException(
                'El header ' . self::TIMESTAMP_HEADER . ' no es un número de segundos Unix.');
        }

        return (int) $digits;
    }

    // Estricto, como la librería de referencia: un cuerpo o un header que no es UTF-8 válido se rechaza aunque esté
    // firmado sobre sus bytes. preg_match con 'u' valida UTF-8 (rechaza sustitutos, secuencias sobrelargas y truncadas)
    // sin depender de mbstring.
    private static function signedContent(string $id, string $timestamp, string $body): string
    {
        $content = $id . '.' . $timestamp . '.' . $body;
        if (preg_match('//u', $content) !== 1) {
            throw new WebhookVerificationException('El cuerpo o un header no es texto UTF-8 válido.');
        }

        return $content;
    }

    /**
     * @param string[] $keys
     */
    private static function ensureSigned(array $keys, string $signedContent, string $signatureHeader): void
    {
        $expected = array_map(
            static function (string $key) use ($signedContent): string {
                return base64_encode(hash_hmac('sha256', $signedContent, $key, true));
            },
            $keys
        );

        foreach (explode(self::SIGNATURE_SEPARATOR, $signatureHeader) as $entry) {
            $signature = self::v1Signature($entry);
            if ($signature === null) {
                continue;
            }
            foreach ($expected as $candidate) {
                // hash_equals compara en tiempo constante; el largo de una firma v1 (44) es público.
                if (hash_equals($candidate, $signature)) {
                    return;
                }
            }
        }

        throw new WebhookVerificationException(
            'Ninguna firma ' . self::SIGNATURE_VERSION . ' del header ' . self::SIGNATURE_HEADER
            . ' coincide con el cuerpo y el secreto.');
    }

    // La firma de una entrada 'v1,<base64>'; null si es de otra versión o no tiene versión.
    private static function v1Signature(string $entry): ?string
    {
        $parts = explode(self::VERSION_SEPARATOR, $entry, 2);

        return count($parts) === 2 && $parts[0] === self::SIGNATURE_VERSION ? $parts[1] : null;
    }

    /**
     * Contenedor de funciones: no se instancia.
     */
    private function __construct()
    {
    }
}
