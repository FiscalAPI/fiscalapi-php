<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Models\WebhookEvent;
use Fiscalapi\Webhooks\Webhook;
use Fiscalapi\Webhooks\WebhookVerificationException;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

/**
 * Webhook::verify(): la firma Standard Webhooks (HMAC-SHA256 sobre webhook-id.webhook-timestamp.cuerpo) y la tolerancia
 * del timestamp. Los mismos escenarios viven en los SDK de .NET, Node.js, Python y Java. El momento de la verificación se
 * fija con FixedClockWebhook (el método privado Webhook::verifyAt()), así el vector oficial (de 2021) verifica sin
 * depender del reloj.
 */
final class WebhookVerifyTest extends TestCase
{
    // El vector publicado por Standard Webhooks (https://github.com/standard-webhooks/standard-webhooks): no es un secreto.
    private const OFFICIAL_SECRET = 'whsec_MfKQ9r8GKYqrTwjUPD8ILPZIo2LaLaSw';
    private const OFFICIAL_ID = 'msg_p5jXN8AQM9LWM0D4loKWxJek';
    private const OFFICIAL_TIMESTAMP = 1614265330;
    private const OFFICIAL_BODY = '{"test": 2432232314}';
    private const OFFICIAL_SIGNATURE = 'v1,g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE=';

    private const MALFORMED_SECRET = 'whsec_%%no-es-base64%%';

    // Fragmento del secreto oficial: ningún mensaje ni traza debe contenerlo.
    private const OFFICIAL_SECRET_FRAGMENT = 'MfKQ9r8GKYqrTwjUPD8ILPZIo2LaLa';

    public function testTheOnlyPublicMethodIsVerify(): void
    {
        // El reloj fijo de las pruebas (verifyAt) no es parte de la API pública.
        self::assertSame(['verify'], get_class_methods(Webhook::class));
    }

    public function testTheOfficialVectorVerifies(): void
    {
        $event = $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(), self::OFFICIAL_BODY);

        self::assertInstanceOf(WebhookEvent::class, $event);
    }

    public function testATamperedSignatureIsRejected(): void
    {
        $headers = $this->officialHeaders(['webhook-signature' => 'v1,g0hM9SsF+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE=']);

        $this->assertRejected('Ninguna firma', function () use ($headers): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $headers, self::OFFICIAL_BODY);
        });
    }

    public function testATamperedBodyIsRejected(): void
    {
        $this->assertRejected('Ninguna firma', function (): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(), '{"test": 2432232315}');
        });
    }

    public function testABodyReformattedAfterSigningIsRejected(): void
    {
        // Mismo JSON sin el espacio: verificar exige el cuerpo crudo, no uno reserializado.
        $this->assertRejected('Ninguna firma', function (): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(), '{"test":2432232314}');
        });
    }

    public function testATamperedIdIsRejected(): void
    {
        $this->assertRejected('Ninguna firma', function (): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-id' => 'msg_otro']),
                self::OFFICIAL_BODY);
        });
    }

    public function testATimestampOlderThanTheToleranceIsRejectedAndTheBoundaryIsAccepted(): void
    {
        $tolerance = Webhook::DEFAULT_TOLERANCE_SECONDS;

        self::assertInstanceOf(WebhookEvent::class,
            $this->verifyAt(self::OFFICIAL_TIMESTAMP + $tolerance, $this->officialHeaders(), self::OFFICIAL_BODY));
        $this->assertRejected('webhook-timestamp', function () use ($tolerance): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP + $tolerance + 1, $this->officialHeaders(), self::OFFICIAL_BODY);
        });
    }

    public function testATimestampFurtherInTheFutureThanTheToleranceIsRejectedAndTheBoundaryIsAccepted(): void
    {
        $tolerance = Webhook::DEFAULT_TOLERANCE_SECONDS;

        self::assertInstanceOf(WebhookEvent::class,
            $this->verifyAt(self::OFFICIAL_TIMESTAMP - $tolerance, $this->officialHeaders(), self::OFFICIAL_BODY));
        $this->assertRejected('difiere', function () use ($tolerance): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP - $tolerance - 1, $this->officialHeaders(), self::OFFICIAL_BODY);
        });
    }

    public function testTheDefaultToleranceIsFiveMinutes(): void
    {
        self::assertSame(300, Webhook::DEFAULT_TOLERANCE_SECONDS);
    }

    public function testACustomToleranceReplacesTheDefault(): void
    {
        $tolerance = 30;

        self::assertInstanceOf(WebhookEvent::class, $this->verifyAt(self::OFFICIAL_TIMESTAMP + $tolerance,
            $this->officialHeaders(), self::OFFICIAL_BODY, self::OFFICIAL_SECRET, $tolerance));
        $this->assertRejected('difiere', function () use ($tolerance): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP + $tolerance + 1, $this->officialHeaders(), self::OFFICIAL_BODY,
                self::OFFICIAL_SECRET, $tolerance);
        });
    }

    public function testANegativeToleranceIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tolerancia');

        Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, self::OFFICIAL_SECRET, -1);
    }

    /**
     * @return array<string, array{string}>
     */
    public function notUnixSecondsProvider(): array
    {
        return [
            'decimal' => ['1614265330.5'],
            'con signo' => ['-1614265330'],
            'con espacio' => [' 1614265330'],
            'fuera de int64' => ['1614265330000000000000'],
            'texto' => ['abc'],
            // preg_match con '$' aceptaría un salto de línea final; el patrón usa '\z'.
            'salto de línea final' => ["1614265330\n"],
        ];
    }

    /**
     * @dataProvider notUnixSecondsProvider
     */
    public function testATimestampThatIsNotUnixSecondsIsRejected(string $timestamp): void
    {
        $this->assertRejected('segundos Unix', function () use ($timestamp): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-timestamp' => $timestamp]),
                self::OFFICIAL_BODY);
        });
    }

    public function testTheLargestUnixTimestampIsRejectedByTheToleranceWithoutOverflowing(): void
    {
        if (PHP_INT_SIZE < 8) {
            self::markTestSkipped('El límite de 64 bits solo aplica a un PHP de 64 bits.');
        }

        $this->assertRejected('difiere', function (): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-timestamp' => (string) PHP_INT_MAX]),
                self::OFFICIAL_BODY);
        });
    }

    public function testATimestampAlteredWithinTheToleranceIsRejectedBecauseItIsSigned(): void
    {
        $alteredTimestamp = self::OFFICIAL_TIMESTAMP + 1;

        $this->assertRejected('Ninguna firma', function () use ($alteredTimestamp): void {
            $this->verifyAt($alteredTimestamp, $this->officialHeaders(['webhook-timestamp' => (string) $alteredTimestamp]),
                self::OFFICIAL_BODY);
        });
    }

    /**
     * @return array<string, array{string}>
     */
    public function withoutValidV1SignatureProvider(): array
    {
        return [
            'versión v1a' => ['v1a,g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE='],
            'versión v2' => ['v2,g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE='],
            'sin versión' => ['g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE='],
            'v1 vacía' => ['v1,'],
            'v1 no base64' => ['v1,!!!'],
            'v1 sin relleno' => ['v1,g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE'],
        ];
    }

    /**
     * @dataProvider withoutValidV1SignatureProvider
     */
    public function testASignatureHeaderWithoutAValidV1SignatureIsRejected(string $signature): void
    {
        $this->assertRejected('Ninguna firma', function () use ($signature): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-signature' => $signature]),
                self::OFFICIAL_BODY);
        });
    }

    /**
     * En PHP el cuerpo es un string de bytes: el equivalente de un sustituto suelto son bytes que no son UTF-8 válido.
     *
     * @return array<string, array{string}>
     */
    public function invalidUtf8Provider(): array
    {
        return [
            'sustituto suelto U+D800 codificado' => ["\xED\xA0\x80"],
            'byte 0xFF' => ["\xFF"],
            'secuencia sobrelarga' => ["\xC0\xAF"],
            'secuencia truncada' => ["\xE2\x82"],
        ];
    }

    /**
     * @dataProvider invalidUtf8Provider
     */
    public function testABodyThatIsNotValidUtf8IsRejectedEvenWhenSignedOverItsBytes(string $invalid): void
    {
        $body = '{"test": "' . $invalid . '"}';
        $signature = BackWebhookSigner::sign(self::OFFICIAL_ID, self::OFFICIAL_TIMESTAMP, $body, self::OFFICIAL_SECRET);

        $this->assertRejected('UTF-8', function () use ($signature, $body): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-signature' => $signature]), $body);
        });
    }

    public function testContentAfterTheSignedEventIsRejected(): void
    {
        $body = '{"id":"evt_1"} {"id":"evt_2"}';
        $signature = BackWebhookSigner::sign(self::OFFICIAL_ID, self::OFFICIAL_TIMESTAMP, $body, self::OFFICIAL_SECRET);

        $exception = $this->assertRejected('evento', function () use ($signature, $body): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-signature' => $signature]), $body);
        });
        self::assertInstanceOf(JsonException::class, $exception->getPrevious());
    }

    /**
     * @return array<string, array{string}>
     */
    public function headerNameProvider(): array
    {
        return [
            'webhook-id' => ['webhook-id'],
            'webhook-timestamp' => ['webhook-timestamp'],
            'webhook-signature' => ['webhook-signature'],
        ];
    }

    /**
     * @dataProvider headerNameProvider
     */
    public function testAMissingHeaderIsRejectedNamingIt(string $missing): void
    {
        $headers = $this->officialHeaders();
        unset($headers[$missing]);

        $this->assertRejected("Falta el header {$missing}.", function () use ($headers): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $headers, self::OFFICIAL_BODY);
        });
    }

    /**
     * @dataProvider headerNameProvider
     */
    public function testAnEmptyHeaderIsRejectedAsMissing(string $empty): void
    {
        $this->assertRejected("Falta el header {$empty}.", function () use ($empty): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders([$empty => '']), self::OFFICIAL_BODY);
        });
        $this->assertRejected("Falta el header {$empty}.", function () use ($empty): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders([$empty => []]), self::OFFICIAL_BODY);
        });
    }

    public function testHeaderNamesAreCaseInsensitive(): void
    {
        // Como los entrega getallheaders().
        $headers = [
            'Webhook-Id' => self::OFFICIAL_ID,
            'WEBHOOK-TIMESTAMP' => (string) self::OFFICIAL_TIMESTAMP,
            'Webhook-Signature' => self::OFFICIAL_SIGNATURE,
        ];

        self::assertInstanceOf(WebhookEvent::class,
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $headers, self::OFFICIAL_BODY));
    }

    public function testHeaderNamesAreComparedAsAsciiUnderATurkishLocale(): void
    {
        // Con un locale turco de un byte, strtolower() de PHP < 8.2 convierte 'I' en 'ı' (sin punto): 'WEBHOOK-ID' ya no
        // sería 'webhook-id'. Los nombres de header son ASCII y se comparan como tales.
        $previous = setlocale(LC_CTYPE, '0');
        $turkish = setlocale(LC_CTYPE, 'tr_TR.ISO8859-9', 'tr_TR.ISO-8859-9', 'tr_TR', 'Turkish_Turkey.1254', 'turkish');
        if ($turkish === false) {
            self::markTestSkipped('No hay un locale turco instalado.');
        }

        try {
            $headers = [
                'WEBHOOK-ID' => self::OFFICIAL_ID,
                'WEBHOOK-TIMESTAMP' => (string) self::OFFICIAL_TIMESTAMP,
                'WEBHOOK-SIGNATURE' => self::OFFICIAL_SIGNATURE,
            ];

            self::assertInstanceOf(WebhookEvent::class,
                $this->verifyAt(self::OFFICIAL_TIMESTAMP, $headers, self::OFFICIAL_BODY));
        } finally {
            setlocale(LC_CTYPE, (string) $previous);
        }
    }

    public function testHeaderValuesAsListsVerifyLikePsr7AndSymfony(): void
    {
        // PSR-7 getHeaders() conserva el nombre; Symfony/Laravel headers->all() lo pasa a minúsculas. Ambos dan listas.
        $headers = [
            'Webhook-Id' => [self::OFFICIAL_ID],
            'webhook-timestamp' => [(string) self::OFFICIAL_TIMESTAMP],
            'Webhook-Signature' => [self::OFFICIAL_SIGNATURE],
        ];

        self::assertInstanceOf(WebhookEvent::class,
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $headers, self::OFFICIAL_BODY));
    }

    public function testAHeaderValueThatIsNotTextIsRefusedAsAnArgumentError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('webhook-timestamp');

        $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-timestamp' => self::OFFICIAL_TIMESTAMP]),
            self::OFFICIAL_BODY);
    }

    public function testASecretWithoutTheWhsecPrefixVerifies(): void
    {
        self::assertInstanceOf(WebhookEvent::class, $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(),
            self::OFFICIAL_BODY, substr(self::OFFICIAL_SECRET, strlen('whsec_'))));
    }

    public function testAnyValidV1SignatureAmongSeveralVerifies(): void
    {
        $signature = 'v1,AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= v1a,ignorada malformada ' . self::OFFICIAL_SIGNATURE;

        self::assertInstanceOf(WebhookEvent::class, $this->verifyAt(self::OFFICIAL_TIMESTAMP,
            $this->officialHeaders(['webhook-signature' => $signature]), self::OFFICIAL_BODY));
    }

    public function testAnyOfSeveralSecretsVerifiesDuringARotation(): void
    {
        $otherSecret = BackWebhookSigner::generateSecret();

        self::assertInstanceOf(WebhookEvent::class, $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(),
            self::OFFICIAL_BODY, [$otherSecret, self::OFFICIAL_SECRET]));
        $this->assertRejected('Ninguna firma', function () use ($otherSecret): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(), self::OFFICIAL_BODY, [$otherSecret]);
        });
    }

    /**
     * @return array<string, array{string}>
     */
    public function malformedSecretProvider(): array
    {
        return [
            'vacío' => [''],
            'solo el prefijo' => ['whsec_'],
            'no base64' => [self::MALFORMED_SECRET],
            // base64_decode(…, true) los acepta: el secreto se exige en base64 canónico.
            'truncado, sin relleno' => ['whsec_MfKQ9r8GKYqrTwjUPD8ILPZIo2LaLaS'],
            'con salto de línea final' => [self::OFFICIAL_SECRET . "\n"],
            'con espacio interno' => ['whsec_MfKQ9r8GKYqrTwjU PD8ILPZIo2LaLaSw'],
        ];
    }

    /**
     * @dataProvider malformedSecretProvider
     */
    public function testAMalformedSecretIsRefusedAsAConfigurationError(string $secret): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(), self::OFFICIAL_BODY, $secret);
    }

    public function testANonCanonicalSecretInAListNamesItsPositionWithoutItsValue(): void
    {
        $exception = $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY,
                [self::OFFICIAL_SECRET, self::OFFICIAL_SECRET . "\n"]);
        });

        self::assertStringContainsString('posición 1', $exception->getMessage());
        self::assertStringNotContainsString(self::OFFICIAL_SECRET_FRAGMENT, $exception->getMessage());
    }

    public function testAFailedVerificationDoesNotExposeTheSecretOrTheKeyInTheTrace(): void
    {
        // Sin php.ini (y en las imágenes de Docker) zend.exception_ignore_args vale 0: la traza lleva los argumentos.
        $previous = ini_set('zend.exception_ignore_args', '0');
        try {
            // Firmada con otro secreto y al momento: falla en la comparación de firmas, con las llaves ya decodificadas.
            $headers = BackWebhookSigner::headers(self::OFFICIAL_ID, time(), self::OFFICIAL_BODY,
                BackWebhookSigner::generateSecret());
            try {
                Webhook::verify($headers, self::OFFICIAL_BODY, [self::OFFICIAL_SECRET]);
                self::fail('Se esperaba WebhookVerificationException.');
            } catch (WebhookVerificationException $exception) {
                $this->assertTraceDoesNotExpose($exception);
            }

            try {
                Webhook::verify($headers, self::OFFICIAL_BODY, self::OFFICIAL_SECRET . "\n");
                self::fail('Se esperaba InvalidArgumentException.');
            } catch (InvalidArgumentException $exception) {
                $this->assertTraceDoesNotExpose($exception);
            }
        } finally {
            ini_set('zend.exception_ignore_args', (string) $previous);
        }
    }

    public function testAMalformedSecretInAListNamesItsPositionWithoutItsValue(): void
    {
        $inList = $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, [self::OFFICIAL_SECRET, self::MALFORMED_SECRET]);
        });
        self::assertStringContainsString('posición 1', $inList->getMessage());
        self::assertStringNotContainsString('no-es-base64', $inList->getMessage());

        $nullInList = $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, [self::OFFICIAL_SECRET, null]);
        });
        self::assertStringContainsString('posición 1 es null', $nullInList->getMessage());

        $single = $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, self::MALFORMED_SECRET);
        });
        self::assertStringNotContainsString('no-es-base64', $single->getMessage());
        self::assertStringNotContainsString('posición', $single->getMessage());
    }

    public function testMissingArgumentsAreRefused(): void
    {
        $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, null);
        });
        $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, []);
        });
        $this->assertArgumentError(function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, 12345);
        });
        $this->assertTypeError(function (): void {
            Webhook::verify(null, self::OFFICIAL_BODY, self::OFFICIAL_SECRET);
        });
        $this->assertTypeError(function (): void {
            Webhook::verify($this->officialHeaders(), null, self::OFFICIAL_SECRET);
        });
    }

    /**
     * @return array<string, array{string}>
     */
    public function signedBodyThatIsNotAnEventProvider(): array
    {
        return [
            'no es JSON' => ['no es json'],
            'JSON null' => ['null'],
            'arreglo JSON' => ['[{"id":"evt_1"}]'],
            'texto JSON' => ['"evt_1"'],
        ];
    }

    /**
     * @dataProvider signedBodyThatIsNotAnEventProvider
     */
    public function testACorrectlySignedBodyThatIsNotAnEventIsRejected(string $body): void
    {
        $signature = BackWebhookSigner::sign(self::OFFICIAL_ID, self::OFFICIAL_TIMESTAMP, $body, self::OFFICIAL_SECRET);

        $this->assertRejected('evento', function () use ($signature, $body): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-signature' => $signature]), $body);
        });
    }

    public function testABodyThatIsNotJsonKeepsTheCause(): void
    {
        $body = 'no es json';
        $signature = BackWebhookSigner::sign(self::OFFICIAL_ID, self::OFFICIAL_TIMESTAMP, $body, self::OFFICIAL_SECRET);

        $exception = $this->assertRejected('evento', function () use ($signature, $body): void {
            $this->verifyAt(self::OFFICIAL_TIMESTAMP, $this->officialHeaders(['webhook-signature' => $signature]), $body);
        });
        self::assertInstanceOf(JsonException::class, $exception->getPrevious());
    }

    public function testThePublicMethodVerifiesAgainstTheCurrentTime(): void
    {
        $timestamp = time();
        $headers = BackWebhookSigner::headers(self::OFFICIAL_ID, $timestamp, self::OFFICIAL_BODY, self::OFFICIAL_SECRET);

        self::assertInstanceOf(WebhookEvent::class, Webhook::verify($headers, self::OFFICIAL_BODY, self::OFFICIAL_SECRET));
        self::assertInstanceOf(WebhookEvent::class, Webhook::verify($headers, self::OFFICIAL_BODY, [self::OFFICIAL_SECRET]));
        $this->assertRejected('difiere', function (): void {
            Webhook::verify($this->officialHeaders(), self::OFFICIAL_BODY, self::OFFICIAL_SECRET);
        });
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function officialHeaders(array $overrides = []): array
    {
        return array_merge([
            'webhook-id' => self::OFFICIAL_ID,
            'webhook-timestamp' => (string) self::OFFICIAL_TIMESTAMP,
            'webhook-signature' => self::OFFICIAL_SIGNATURE,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $headers
     * @param mixed $secret
     */
    private function verifyAt(int $now, array $headers, string $body, $secret = self::OFFICIAL_SECRET,
        int $tolerance = Webhook::DEFAULT_TOLERANCE_SECONDS): WebhookEvent
    {
        return FixedClockWebhook::verifyAt($headers, $body, $secret, $tolerance, $now);
    }

    /**
     * La llave (los bytes del secreto) nunca aparece en la traza, ni como texto ni al volcar un objeto con print_r o
     * var_dump. El secreto tal como se configuró solo se puede ocultar desde PHP 8.2 (#[\SensitiveParameter]): en 7.4
     * queda en los argumentos de verify(); por eso el README recomienda zend.exception_ignore_args=On.
     */
    private function assertTraceDoesNotExpose(Throwable $exception): void
    {
        $key = (string) base64_decode(substr(self::OFFICIAL_SECRET, strlen('whsec_')), true);
        $dump = $exception->getTraceAsString() . $this->dumpTraceArguments($exception->getTrace());

        // assertFalse y no assertStringNotContainsString: al fallar, este volcaría la traza (y la llave) en la salida.
        self::assertFalse(strpos($dump, $key) !== false, 'La llave aparece en la traza.');
        if (PHP_VERSION_ID >= 80200) {
            self::assertFalse(strpos($dump, self::OFFICIAL_SECRET_FRAGMENT) !== false, 'El secreto aparece en la traza.');
        }
    }

    /**
     * Los argumentos de la traza como texto: los escalares tal cual y los objetos del SDK (o el SensitiveParameterValue
     * de PHP 8.2) como los mostraría un logger con print_r y var_dump. Los objetos de PHPUnit y de las pruebas no se
     * vuelcan: arrastran el estado de la corrida, con los datos de otras pruebas.
     *
     * @param array<int, array<string, mixed>> $trace
     */
    private function dumpTraceArguments(array $trace): string
    {
        $dump = '';
        array_walk_recursive($trace, static function ($value) use (&$dump): void {
            if (is_scalar($value)) {
                $dump .= $value . "\n";
            } elseif (is_object($value) && self::isSdkOrRedactedValue($value)) {
                ob_start();
                var_dump($value);
                $dump .= print_r($value, true) . ob_get_clean();
            }
        });

        return $dump;
    }

    private static function isSdkOrRedactedValue(object $value): bool
    {
        $class = get_class($value);

        return $class === 'SensitiveParameterValue'
            || (strpos($class, 'Fiscalapi\\') === 0 && strpos($class, 'Fiscalapi\\Tests\\') !== 0);
    }

    private function assertRejected(string $expectedMessage, callable $verify): WebhookVerificationException
    {
        try {
            $verify();
        } catch (WebhookVerificationException $exception) {
            self::assertStringContainsString($expectedMessage, $exception->getMessage());

            return $exception;
        }

        self::fail('Se esperaba WebhookVerificationException.');
    }

    private function assertArgumentError(callable $verify): InvalidArgumentException
    {
        try {
            $verify();
        } catch (InvalidArgumentException $exception) {
            return $exception;
        } catch (Throwable $other) {
            self::fail('Se esperaba InvalidArgumentException y se lanzó ' . get_class($other) . ': ' . $other->getMessage());
        }

        self::fail('Se esperaba InvalidArgumentException.');
    }

    private function assertTypeError(callable $verify): void
    {
        try {
            $verify();
        } catch (TypeError $exception) {
            self::addToAssertionCount(1);

            return;
        }

        self::fail('Se esperaba TypeError.');
    }
}
