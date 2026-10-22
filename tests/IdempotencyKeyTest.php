<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\InvoiceService;
use Fiscalapi\Services\StampService;
use PHPUnit\Framework\TestCase;

/**
 * Llave de idempotencia de crear factura y de transferir timbres. Con la llave, la petición lleva el header
 * Idempotency-Key con ese valor; sin ella, no lleva headers. El SDK no genera la llave ni reintenta: solo la pasa.
 */
final class IdempotencyKeyTest extends TestCase
{
    private const IDEMPOTENCY_KEY = 'pos-01-F-1001';
    private const PERSON_ID = '4b1c2d3e-0000-4000-8000-000000000001';

    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
    }

    private static function newInvoice(): array
    {
        return [
            'versionCode' => '4.0',
            'series' => 'SDK-F',
            'date' => '2026-10-15T12:30:00',
            'paymentFormCode' => '01',
            'currencyCode' => 'MXN',
            'typeCode' => 'I',
            'expeditionZipCode' => '06000',
            'issuer' => ['id' => self::PERSON_ID],
            'recipient' => ['id' => self::PERSON_ID],
            'items' => [['id' => 'producto-1', 'quantity' => 1, 'discount' => 0]],
            'paymentMethodCode' => 'PUE',
        ];
    }

    private static function newTransfer(): array
    {
        return [
            'fromPersonId' => '1',
            'toPersonId' => self::PERSON_ID,
            'amount' => 25,
        ];
    }

    /**
     * @return array{method: string, uri: string, options: array}
     */
    private function singleRequest(): array
    {
        $requests = $this->api->getRequests();
        self::assertCount(1, $requests);

        return $requests[0];
    }

    public function testCreateInvoiceSendsTheKeyAsIdempotencyKeyHeader(): void
    {
        (new InvoiceService($this->api))->create(self::newInvoice(), self::IDEMPOTENCY_KEY);

        $request = $this->singleRequest();
        self::assertSame('POST', $request['method']);
        self::assertSame('/invoices', $request['uri']);
        self::assertSame(self::newInvoice(), $request['options']['data']);
        self::assertSame(['Idempotency-Key' => self::IDEMPOTENCY_KEY], $request['options']['headers']);
    }

    public function testCreateInvoiceWithoutAKeyDoesNotSendHeaders(): void
    {
        (new InvoiceService($this->api))->create(self::newInvoice());

        $request = $this->singleRequest();
        self::assertSame(self::newInvoice(), $request['options']['data']);
        self::assertArrayNotHasKey('headers', $request['options']);
    }

    public function testTransferStampsSendsTheKeyAsIdempotencyKeyHeader(): void
    {
        (new StampService($this->api))->transferStamps(self::newTransfer(), self::IDEMPOTENCY_KEY);

        $request = $this->singleRequest();
        self::assertSame('POST', $request['method']);
        self::assertSame('/stamps', $request['uri']);
        self::assertSame(self::newTransfer(), $request['options']['data']);
        self::assertSame(['Idempotency-Key' => self::IDEMPOTENCY_KEY], $request['options']['headers']);
    }

    public function testTransferStampsWithoutAKeyDoesNotSendHeaders(): void
    {
        (new StampService($this->api))->transferStamps(self::newTransfer());

        $request = $this->singleRequest();
        self::assertSame(self::newTransfer(), $request['options']['data']);
        self::assertArrayNotHasKey('headers', $request['options']);
    }
}
