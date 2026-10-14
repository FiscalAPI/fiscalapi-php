<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use DateTimeImmutable;
use Fiscalapi\Models\WebhookEvent;
use Fiscalapi\Models\WebhookEventTypes;
use Fiscalapi\Webhooks\Webhook;
use Fiscalapi\Webhooks\WebhookVerificationException;
use PHPUnit\Framework\TestCase;

/**
 * Interoperabilidad con el back: una entrega firmada como la firma WebhookSigner de fapi-back (dos secretos durante una
 * rotación, el vigente primero) y con el sobre tal como lo serializa el back (camelCase, sin espacios, createdAt al
 * segundo con Z y las nulas presentes) verifica con Webhook::verify() y se lee completa en WebhookEvent.
 */
final class WebhookBackInteropTest extends TestCase
{
    private const EVENT_ID = '0f6b2c1e-7d3a-4e8b-9c51-2a4d6e8f0b13';
    private const PERSON_ID = '4b1c2d3e-0000-4000-8000-000000000002';
    private const INVOICE_ID = '9f8e7d6c-0000-4000-8000-0000000000a1';
    private const DOWNLOAD_REQUEST_ID = '3c2b1a09-0000-4000-8000-0000000000b7';
    private const WEBHOOK_ID = '6a5b4c3d-0000-4000-8000-0000000000c9';
    private const TIMESTAMP = 1791830873; // 2026-10-12T18:47:53Z, el createdAt de los sobres

    // Los sobres de WebhookEnvelopeTests de fapi-back, con ids fijos.
    private const CANCELED_INVOICE_BODY =
        '{"id":"0f6b2c1e-7d3a-4e8b-9c51-2a4d6e8f0b13","type":"invoice.canceled","createdAt":"2026-10-12T18:47:53Z",'
        . '"personId":"4b1c2d3e-0000-4000-8000-000000000002","data":{"relatedObject":{"id":'
        . '"9f8e7d6c-0000-4000-8000-0000000000a1","type":"invoice","url":'
        . '"/api/v4/invoices/9f8e7d6c-0000-4000-8000-0000000000a1"},"status":"X","previousStatus":"C"}}';

    private const COMPLETED_DOWNLOAD_REQUEST_BODY =
        '{"id":"0f6b2c1e-7d3a-4e8b-9c51-2a4d6e8f0b13","type":"download_request.completed",'
        . '"createdAt":"2026-10-12T18:47:53Z","personId":null,"data":{"relatedObject":{"id":'
        . '"3c2b1a09-0000-4000-8000-0000000000b7","type":"download_request","url":'
        . '"/api/v4/download-requests/3c2b1a09-0000-4000-8000-0000000000b7"},"status":null,"previousStatus":null}}';

    // El sobre de "Enviar prueba" del back (POST /api/v4/webhooks/{id}/test): el recurso es el webhook mismo.
    private const WEBHOOK_TEST_BODY =
        '{"id":"0f6b2c1e-7d3a-4e8b-9c51-2a4d6e8f0b13","type":"webhook.test","createdAt":"2026-10-12T18:47:53Z",'
        . '"personId":null,"data":{"relatedObject":{"id":"6a5b4c3d-0000-4000-8000-0000000000c9","type":'
        . '"webhook","url":"/api/v4/webhooks/6a5b4c3d-0000-4000-8000-0000000000c9"},"status":null,'
        . '"previousStatus":null}}';

    public function testABackDeliverySignedDuringARotationVerifiesWithEitherSecretAndReadsEveryField(): void
    {
        $current = BackWebhookSigner::generateSecret();
        $previous = BackWebhookSigner::generateSecret();
        $headers = BackWebhookSigner::headers(self::EVENT_ID, self::TIMESTAMP, self::CANCELED_INVOICE_BODY, $current,
            $previous);

        foreach ([$current, $previous] as $receiverSecret) {
            $event = $this->deliver($headers, self::CANCELED_INVOICE_BODY, $receiverSecret);

            self::assertSame(self::EVENT_ID, $event->getId());
            self::assertSame(WebhookEventTypes::INVOICE_CANCELED, $event->getType());
            self::assertEquals(new DateTimeImmutable('2026-10-12T18:47:53Z'), $event->getCreatedAt());
            self::assertSame('UTC', $event->getCreatedAt()->getTimezone()->getName());
            self::assertSame(self::PERSON_ID, $event->getPersonId());
            self::assertSame(self::INVOICE_ID, $event->getData()->getRelatedObject()->getId());
            self::assertSame('invoice', $event->getData()->getRelatedObject()->getType());
            self::assertSame('/api/v4/invoices/' . self::INVOICE_ID, $event->getData()->getRelatedObject()->getUrl());
            self::assertSame('X', $event->getData()->getStatus());
            self::assertSame('C', $event->getData()->getPreviousStatus());
        }
    }

    public function testABackDeliveryWithoutPersonOrStatusesReadsThemAsNull(): void
    {
        $secret = BackWebhookSigner::generateSecret();
        $headers = BackWebhookSigner::headers(self::EVENT_ID, self::TIMESTAMP, self::COMPLETED_DOWNLOAD_REQUEST_BODY,
            $secret);

        $event = $this->deliver($headers, self::COMPLETED_DOWNLOAD_REQUEST_BODY, $secret);

        self::assertSame(WebhookEventTypes::DOWNLOAD_REQUEST_COMPLETED, $event->getType());
        self::assertNull($event->getPersonId());
        self::assertSame(self::DOWNLOAD_REQUEST_ID, $event->getData()->getRelatedObject()->getId());
        self::assertSame('download_request', $event->getData()->getRelatedObject()->getType());
        self::assertSame('/api/v4/download-requests/' . self::DOWNLOAD_REQUEST_ID,
            $event->getData()->getRelatedObject()->getUrl());
        self::assertNull($event->getData()->getStatus());
        self::assertNull($event->getData()->getPreviousStatus());
    }

    public function testAnUnknownTypeAndUnknownFieldsAreReadWithoutError(): void
    {
        $body = '{"id":"evt_1","type":"invoice.future_state","createdAt":"2026-10-12T18:47:53Z","personId":null,'
            . '"extra":1,"data":{"relatedObject":{"id":"r1","type":"future_resource","url":"/api/v4/future/r1",'
            . '"extra":true},"status":"Z","previousStatus":null,"more":{"a":[1,2]}}}';
        $secret = BackWebhookSigner::generateSecret();
        $headers = BackWebhookSigner::headers('evt_1', self::TIMESTAMP, $body, $secret);

        $event = $this->deliver($headers, $body, $secret);

        self::assertSame('invoice.future_state', $event->getType());
        self::assertSame('future_resource', $event->getData()->getRelatedObject()->getType());
        self::assertSame('Z', $event->getData()->getStatus());
    }

    public function testABackWebhookTestDeliveryPointsToTheWebhookWithoutPersonOrStatuses(): void
    {
        $secret = BackWebhookSigner::generateSecret();
        $headers = BackWebhookSigner::headers(self::EVENT_ID, self::TIMESTAMP, self::WEBHOOK_TEST_BODY, $secret);

        $event = $this->deliver($headers, self::WEBHOOK_TEST_BODY, $secret);

        self::assertSame(WebhookEventTypes::WEBHOOK_TEST, $event->getType());
        self::assertNull($event->getPersonId());
        self::assertSame(self::WEBHOOK_ID, $event->getData()->getRelatedObject()->getId());
        self::assertSame('webhook', $event->getData()->getRelatedObject()->getType());
        self::assertSame('/api/v4/webhooks/' . self::WEBHOOK_ID,
            $event->getData()->getRelatedObject()->getUrl());
        self::assertNull($event->getData()->getStatus());
        self::assertNull($event->getData()->getPreviousStatus());
    }

    public function testTheEventTypeConstantsAreTheTypesOfTheBack(): void
    {
        self::assertSame('webhook.test', WebhookEventTypes::WEBHOOK_TEST);
        self::assertSame('invoice.cancellation_requested', WebhookEventTypes::INVOICE_CANCELLATION_REQUESTED);
        self::assertSame('invoice.canceled', WebhookEventTypes::INVOICE_CANCELED);
        self::assertSame('invoice.cancellation_rejected', WebhookEventTypes::INVOICE_CANCELLATION_REJECTED);
        self::assertSame('download_request.completed', WebhookEventTypes::DOWNLOAD_REQUEST_COMPLETED);
        self::assertSame('download_request.abandoned', WebhookEventTypes::DOWNLOAD_REQUEST_ABANDONED);
        self::assertSame('download_rule.suspended', WebhookEventTypes::DOWNLOAD_RULE_SUSPENDED);
    }

    public function testACreatedAtWithAnOffsetIsReadInUtc(): void
    {
        $body = '{"id":"evt_1","type":"invoice.canceled","createdAt":"2026-10-12T12:47:53.250-06:00"}';
        $secret = BackWebhookSigner::generateSecret();

        $event = $this->deliver(BackWebhookSigner::headers('evt_1', self::TIMESTAMP, $body, $secret), $body, $secret);

        self::assertSame('2026-10-12T18:47:53.250000+00:00', $event->getCreatedAt()->format('Y-m-d\TH:i:s.uP'));
        self::assertSame('UTC', $event->getCreatedAt()->getTimezone()->getName());
    }

    public function testASignedJsonObjectWithoutEnvelopeFieldsReadsThemAsNull(): void
    {
        $body = '{"test": 2432232314}';
        $secret = BackWebhookSigner::generateSecret();

        $event = $this->deliver(BackWebhookSigner::headers('evt_1', self::TIMESTAMP, $body, $secret), $body, $secret);

        self::assertNull($event->getId());
        self::assertNull($event->getType());
        self::assertNull($event->getCreatedAt());
        self::assertNull($event->getPersonId());
        self::assertNull($event->getData());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function malformedEnvelopeFieldProvider(): array
    {
        return [
            'id numérico' => ['{"id":5}', 'id'],
            'createdAt que no es fecha' => ['{"createdAt":"ayer"}', 'createdAt'],
            'createdAt imposible' => ['{"createdAt":"2026-02-30T18:47:53Z"}', 'createdAt'],
            'createdAt sin zona' => ['{"createdAt":"2026-10-12T18:47:53"}', 'createdAt'],
            'createdAt sin ceros' => ['{"createdAt":"2026-1-2T18:47:53Z"}', 'createdAt'],
            'createdAt con zona sin dos puntos' => ['{"createdAt":"2026-10-12T18:47:53+0600"}', 'createdAt'],
            'data que no es objeto' => ['{"data":"x"}', 'data'],
            'relatedObject que no es objeto' => ['{"data":{"relatedObject":[]}}', 'data.relatedObject'],
            'url que no es texto' => ['{"data":{"relatedObject":{"url":true}}}', 'data.relatedObject.url'],
        ];
    }

    /**
     * @dataProvider malformedEnvelopeFieldProvider
     */
    public function testAKnownFieldWithTheWrongShapeIsRejectedNamingIt(string $body, string $field): void
    {
        $secret = BackWebhookSigner::generateSecret();

        try {
            $this->deliver(BackWebhookSigner::headers('evt_1', self::TIMESTAMP, $body, $secret), $body, $secret);
            self::fail('Se esperaba WebhookVerificationException.');
        } catch (WebhookVerificationException $exception) {
            self::assertStringContainsString("'{$field}'", $exception->getMessage());
        }
    }

    /**
     * @param array<string, string> $headers
     */
    private function deliver(array $headers, string $body, string $secret): WebhookEvent
    {
        return FixedClockWebhook::verifyAt($headers, $body, [$secret], Webhook::DEFAULT_TOLERANCE_SECONDS,
            self::TIMESTAMP);
    }
}
