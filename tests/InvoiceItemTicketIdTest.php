<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\InvoiceService;
use PHPUnit\Framework\TestCase;

/**
 * 'ticketId' en los conceptos de la factura (Auto CFDI): liga cada concepto de la factura global a su ticket. Los mismos
 * escenarios viven en los SDK de .NET, Node.js, Python y Java: sin 'ticketId' el cuerpo de create() es exactamente el de
 * la versión anterior; con él, cada concepto lo lleva (un ticket puede ir en varios conceptos); un 'ticketId' en null
 * nunca viaja, y el 409 de tickets no disponibles trae 'unavailableTickets' en getJson()['data'].
 */
final class InvoiceItemTicketIdTest extends TestCase
{
    private const ISSUER_ID = '4b1c2d3e-0000-4000-8000-000000000002';
    private const TICKET_ID = '9f8e7d6c-0000-4000-8000-0000000000a1';
    private const OTHER_TICKET_ID = '9f8e7d6c-0000-4000-8000-0000000000a2';

    // Cuerpo que enviaba la versión anterior del SDK para la misma global (json_encode, como lo codifica Guzzle).
    private const PREVIOUS_PAYLOAD =
        '{"versionCode":"4.0","series":"G","number":"1001","date":"2026-10-01T10:00:00","paymentFormCode":"01",'
        . '"paymentMethodCode":"PUE","currencyCode":"MXN","typeCode":"I","expeditionZipCode":"06000","exportCode":"01",'
        . '"paymentConditions":null,"issuer":{"id":"4b1c2d3e-0000-4000-8000-000000000002"},"recipient":'
        . '{"tin":"XAXX010101000","legalName":"PUBLICO EN GENERAL","zipCode":"06000","taxRegimeCode":"616",'
        . '"cfdiUseCode":"S01"},"globalInformation":{"periodicityCode":"04","monthCode":"09","year":2026},"items":['
        . '{"itemCode":"01010101","itemSku":"F-1001","quantity":1,"unitOfMeasurementCode":"ACT","description":"Venta",'
        . '"unitPrice":"100.00","taxObjectCode":"02","itemTaxes":[{"taxCode":"002","taxTypeCode":"Tasa",'
        . '"taxRate":"0.160000","taxFlagCode":"T"}]},'
        . '{"itemCode":"01010101","itemSku":"F-1001","quantity":1,"unitOfMeasurementCode":"ACT","description":"Venta",'
        . '"unitPrice":"50.00","taxObjectCode":"02","itemTaxes":[{"taxCode":"002","taxTypeCode":"Tasa",'
        . '"taxRate":"0.000000","taxFlagCode":"T"}]}]}';

    private const UNAVAILABLE_TICKETS_DETAILS =
        'Uno o más tickets ya no están disponibles para la factura global (solo entra un ticket en estado pending): '
        . "'data.unavailableTickets' dice cuáles y su estado. No se cobró el timbre ni quedó tomado ningún ticket.";

    private FakeFiscalApiHttpClient $api;

    private InvoiceService $invoices;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
        $this->api->respondWith((string) json_encode([
            'data' => ['id' => '9c1d2e3f-0000-4000-8000-0000000000a1'],
            'succeeded' => true,
            'message' => '',
            'details' => '',
            'httpStatusCode' => 200,
        ]));
        $this->invoices = new InvoiceService($this->api);
    }

    /**
     * Un concepto por grupo de impuestos del mismo ticket (IVA 16 % y 0 %); sin $ticketId no lleva la llave.
     *
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private static function concept(string $unitPrice, string $taxRate, array $extra = []): array
    {
        return array_merge([
            'itemCode' => '01010101',
            'itemSku' => 'F-1001',
            'quantity' => 1,
            'unitOfMeasurementCode' => 'ACT',
            'description' => 'Venta',
            'unitPrice' => $unitPrice,
            'taxObjectCode' => '02',
            'itemTaxes' => [
                ['taxCode' => '002', 'taxTypeCode' => 'Tasa', 'taxRate' => $taxRate, 'taxFlagCode' => 'T'],
            ],
        ], $extra);
    }

    /**
     * La global con un null fuera de los conceptos ('paymentConditions'), que el SDK sigue enviando tal cual.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    private static function globalInvoice(array $items): array
    {
        return [
            'versionCode' => '4.0',
            'series' => 'G',
            'number' => '1001',
            'date' => '2026-10-01T10:00:00',
            'paymentFormCode' => '01',
            'paymentMethodCode' => 'PUE',
            'currencyCode' => 'MXN',
            'typeCode' => 'I',
            'expeditionZipCode' => '06000',
            'exportCode' => '01',
            'paymentConditions' => null,
            'issuer' => ['id' => self::ISSUER_ID],
            'recipient' => [
                'tin' => 'XAXX010101000',
                'legalName' => 'PUBLICO EN GENERAL',
                'zipCode' => '06000',
                'taxRegimeCode' => '616',
                'cfdiUseCode' => 'S01',
            ],
            'globalInformation' => ['periodicityCode' => '04', 'monthCode' => '09', 'year' => 2026],
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sentBody(): array
    {
        $requests = $this->api->getRequests();
        self::assertCount(1, $requests);
        self::assertSame('POST', $requests[0]['method']);
        self::assertSame('/invoices', $requests[0]['uri']);

        return $requests[0]['options']['data'];
    }

    public function testWithoutTicketIdCreateSendsExactlyThePreviousPayload(): void
    {
        $this->invoices->create(self::globalInvoice([self::concept('100.00', '0.160000'), self::concept('50.00', '0.000000')]));

        self::assertSame(self::PREVIOUS_PAYLOAD, json_encode($this->sentBody()));
    }

    public function testANullTicketIdIsOmitted(): void
    {
        $this->invoices->create(self::globalInvoice([
            self::concept('100.00', '0.160000', ['ticketId' => null]),
            self::concept('50.00', '0.000000', ['ticketId' => null]),
        ]));

        self::assertSame(self::PREVIOUS_PAYLOAD, json_encode($this->sentBody()));
    }

    public function testWithTicketIdEachItemCarriesItAndNothingElseChanges(): void
    {
        $this->invoices->create(self::globalInvoice([
            self::concept('100.00', '0.160000', ['ticketId' => self::TICKET_ID]),
            self::concept('50.00', '0.000000', ['ticketId' => self::TICKET_ID]),
        ]));

        $expected = json_decode(self::PREVIOUS_PAYLOAD, true);
        foreach ($expected['items'] as $index => $item) {
            $expected['items'][$index]['ticketId'] = self::TICKET_ID;
        }
        self::assertSame($expected, $this->sentBody());
    }

    public function testOnlyTheItemsWithTicketIdCarryIt(): void
    {
        $this->invoices->create(self::globalInvoice([
            self::concept('100.00', '0.160000'),
            self::concept('50.00', '0.000000', ['ticketId' => self::OTHER_TICKET_ID]),
        ]));

        $items = $this->sentBody()['items'];
        self::assertArrayNotHasKey('ticketId', $items[0]);
        self::assertSame(self::OTHER_TICKET_ID, $items[1]['ticketId']);
    }

    public function testUnavailableTicketsIs409WithTheTicketsInData(): void
    {
        $unavailable = [
            ['ticketId' => self::TICKET_ID, 'status' => 'invoiced'],
            ['ticketId' => self::OTHER_TICKET_ID, 'status' => 'globalizing'],
        ];
        $this->api->respondWith((string) json_encode([
            'data' => ['unavailableTickets' => $unavailable],
            'succeeded' => false,
            'message' => "Sorry, it's not me, it's you.",
            'details' => self::UNAVAILABLE_TICKETS_DETAILS,
            'httpStatusCode' => 409,
        ]), 409);

        $response = $this->invoices->create(self::globalInvoice([
            self::concept('100.00', '0.160000', ['ticketId' => self::TICKET_ID]),
            self::concept('50.00', '0.000000', ['ticketId' => self::OTHER_TICKET_ID]),
        ]));

        self::assertSame(409, $response->getStatusCode());
        self::assertFalse($response->isSuccessful());
        self::assertSame(self::UNAVAILABLE_TICKETS_DETAILS, $response->getJson()['details']);
        self::assertSame(['unavailableTickets' => $unavailable], $response->getJson()['data']);
    }
}
