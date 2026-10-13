<?php
declare(strict_types=1);

namespace Fiscalapi\Webhooks;

use DateTimeImmutable;
use DateTimeZone;
use Fiscalapi\Models\WebhookEvent;
use Fiscalapi\Models\WebhookEventData;
use Fiscalapi\Models\WebhookRelatedObject;
use JsonException;
use stdClass;

/**
 * Lee el sobre de un webhook ya verificado. Toda opción de json_decode es explícita, así nada del entorno del integrador
 * cambia cómo se lee. Un campo que el SDK no conoce se ignora (el sobre puede crecer); uno conocido con otra forma rechaza
 * la entrega nombrándolo.
 *
 * @internal Úselo a través de Webhook::verify().
 */
final class WebhookEnvelope
{
    private const NOT_AN_EVENT = 'El cuerpo firmado no es un evento de webhook en JSON';

    // ISO 8601 con zona obligatoria; el back envía 'Z' al segundo. PHP guarda hasta microsegundos.
    private const DATE_PATTERN = '/\A\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?(Z|[+-]\d{2}:\d{2})\z/';

    /**
     * @throws WebhookVerificationException El cuerpo no es un objeto JSON o un campo conocido tiene otra forma.
     */
    public static function read(string $body): WebhookEvent
    {
        $event = self::object(self::decode($body), 'evento');

        return new WebhookEvent(
            self::text($event, 'id'),
            self::text($event, 'type'),
            self::date($event, 'createdAt'),
            self::text($event, 'personId'),
            self::data($event)
        );
    }

    /**
     * @return mixed
     */
    private static function decode(string $body)
    {
        try {
            // Sin JSON_OBJECT_AS_ARRAY: un objeto JSON llega como stdClass y se distingue de un arreglo. json_decode
            // rechaza el contenido que sobra después del evento.
            return json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new WebhookVerificationException(self::NOT_AN_EVENT . '.', 0, $exception);
        }
    }

    private static function data(stdClass $event): ?WebhookEventData
    {
        $data = self::optionalObject($event, 'data', 'data');
        if ($data === null) {
            return null;
        }

        return new WebhookEventData(
            self::relatedObject($data),
            self::text($data, 'status', 'data.'),
            self::text($data, 'previousStatus', 'data.')
        );
    }

    private static function relatedObject(stdClass $data): ?WebhookRelatedObject
    {
        $relatedObject = self::optionalObject($data, 'relatedObject', 'data.relatedObject');
        if ($relatedObject === null) {
            return null;
        }

        $path = 'data.relatedObject.';

        return new WebhookRelatedObject(
            self::text($relatedObject, 'id', $path),
            self::text($relatedObject, 'type', $path),
            self::text($relatedObject, 'url', $path)
        );
    }

    private static function optionalObject(stdClass $parent, string $name, string $path): ?stdClass
    {
        $value = self::field($parent, $name);

        return $value === null ? null : self::object($value, "'{$path}'");
    }

    /**
     * @param mixed $value
     */
    private static function object($value, string $what): stdClass
    {
        if (!$value instanceof stdClass) {
            throw new WebhookVerificationException(self::NOT_AN_EVENT . ": {$what} no es un objeto.");
        }

        return $value;
    }

    private static function text(stdClass $parent, string $name, string $path = ''): ?string
    {
        $value = self::field($parent, $name);
        if ($value !== null && !is_string($value)) {
            throw new WebhookVerificationException(self::NOT_AN_EVENT . ": '{$path}{$name}' no es texto.");
        }

        return $value;
    }

    private static function date(stdClass $parent, string $name): ?DateTimeImmutable
    {
        $value = self::text($parent, $name);
        if ($value === null) {
            return null;
        }

        $date = self::parseDate($value);
        if ($date === null) {
            throw new WebhookVerificationException(
                self::NOT_AN_EVENT . ": '{$name}' no es una fecha ISO 8601 con zona.");
        }

        return $date->setTimezone(new DateTimeZone('UTC'));
    }

    // createFromFormat recorre un día o un mes imposible (30 de febrero) en vez de fallar: se rechaza por sus avisos.
    private static function parseDate(string $value): ?DateTimeImmutable
    {
        if (preg_match(self::DATE_PATTERN, $value, $parts) !== 1) {
            return null;
        }

        $format = $parts[1] === '' ? 'Y-m-d\TH:i:sP' : 'Y-m-d\TH:i:s.uP';
        $date = DateTimeImmutable::createFromFormat($format, $value);
        $errors = DateTimeImmutable::getLastErrors();
        $hasErrors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

        return $date === false || $hasErrors ? null : $date;
    }

    /**
     * @return mixed
     */
    private static function field(stdClass $parent, string $name)
    {
        return property_exists($parent, $name) ? $parent->{$name} : null;
    }

    /**
     * Contenedor de funciones: no se instancia.
     */
    private function __construct()
    {
    }
}
