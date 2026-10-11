<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Models\TicketActorType;
use Fiscalapi\Models\TicketEventType;
use Fiscalapi\Models\TicketStatus;
use Fiscalapi\Services\FiscalApiClient;
use Fiscalapi\Services\TicketService;
use Fiscalapi\Services\TicketServiceInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Servicio de tickets de Auto CFDI (POST/GET/DELETE /api/v4/tickets). Los mismos escenarios viven en los SDK de .NET,
 * Node.js, Python y Java: el cuerpo exacto de la ingesta y el header Idempotency-Key solo si se pasa; las respuestas
 * 201 y 200 de la ingesta; el 409 y el 422 con la forma de error del SDK (en PHP, la respuesta con su status y el
 * cuerpo completo en getJson(), con su 'data'); la query de la lista con status repetido; el detalle con y sin
 * details; el código; la cancelación con DELETE, y un estado que el SDK no conoce, conservado como texto.
 */
final class TicketServiceTest extends TestCase
{
    private const ISSUER_ID = '4b1c2d3e-0000-4000-8000-000000000002';
    private const TICKET_ID = '9f8e7d6c-0000-4000-8000-0000000000a1';
    private const IDEMPOTENCY_KEY = 'pos-01-F-1001';

    private const INGESTED = [
        'id' => self::TICKET_ID,
        'ticketCode' => 'K7M2Q9X4TB3H',
        'qrUrl' => 'https://portal.fiscalapi.com/t/K7M2Q9X4TB3H',
        'status' => 'pending',
        'selfInvoiceEligible' => true,
        'eligibleUntil' => '2026-10-31T23:59:59.000',
        'subtotal' => 100,
        'totalTransferredTaxes' => 16,
        'totalWithheldTaxes' => 0,
        'total' => 116,
    ];

    private const SUMMARY = [
        'id' => self::TICKET_ID,
        'personId' => self::ISSUER_ID,
        'folio' => 'F-1001',
        'series' => 'A',
        'paymentDate' => '2026-09-01T00:00:00.000',
        'paymentFormCode' => '01',
        'currencyCode' => 'MXN',
        'exchangeRate' => null,
        'expeditionZipCode' => '06000',
        'subtotal' => 100,
        'totalTransferredTaxes' => 16,
        'totalWithheldTaxes' => 0,
        'total' => 116,
        'status' => 'pending',
        'selfInvoiceEligible' => true,
        'eligibleUntil' => '2026-09-30T23:59:59.000',
        'invoiceId' => null,
        'globalInvoiceId' => null,
        'createdAt' => '2026-09-01T00:05:00.000',
    ];

    // El cuerpo exacto que recibe el API (Guzzle lo codifica con json_encode): claves camelCase y decimales en texto,
    // que conservan sus ceros finales.
    private const EXPECTED_CREATE_JSON = '{"issuer":{"id":"4b1c2d3e-0000-4000-8000-000000000002"},"folio":"F-1001","series":"A",'
        . '"paymentDate":"2026-10-15T12:30:00","paymentFormCode":"01","currencyCode":"MXN","expeditionZipCode":"06000",'
        . '"total":"116.00","items":[{"itemCode":"50202306","itemSku":"SKU-1","quantity":1.5,"unitOfMeasurementCode":"H87",'
        . '"description":"Refresco","unitPrice":"66.666667","discount":"0.00","taxObjectCode":"02",'
        . '"itemTaxes":[{"taxCode":"002","taxTypeCode":"Tasa","taxRate":"0.160000","taxFlagCode":"T"}]}]}';

    private FakeFiscalApiHttpClient $api;
    private TicketService $tickets;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
        $this->tickets = new TicketService($this->api);
    }

    private static function envelope($data, int $httpStatusCode = 200): string
    {
        return (string) json_encode(['data' => $data, 'succeeded' => true, 'message' => '', 'details' => '', 'httpStatusCode' => $httpStatusCode]);
    }

    private static function newTicket(): array
    {
        return [
            'issuer' => ['id' => self::ISSUER_ID],
            'folio' => 'F-1001',
            'series' => 'A',
            'paymentDate' => '2026-10-15T12:30:00',
            'paymentFormCode' => '01',
            'currencyCode' => 'MXN',
            'expeditionZipCode' => '06000',
            'total' => '116.00',
            'items' => [[
                'itemCode' => '50202306',
                'itemSku' => 'SKU-1',
                'quantity' => 1.5,
                'unitOfMeasurementCode' => 'H87',
                'description' => 'Refresco',
                'unitPrice' => '66.666667',
                'discount' => '0.00',
                'taxObjectCode' => '02',
                'itemTaxes' => [['taxCode' => '002', 'taxTypeCode' => 'Tasa', 'taxRate' => '0.160000', 'taxFlagCode' => 'T']],
            ]],
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

    public function testTheClientExposesTheTicketService(): void
    {
        $client = new FiscalApiClient(new FiscalApiSettings('https://sdk-tests.fiscalapi.invalid', 'sk_test_sdk', 'tenant-sdk'));

        self::assertInstanceOf(TicketServiceInterface::class, $client->getTicketService());
        self::assertSame($client->getTicketService(), $client->getTicketService());
    }

    // a. Cuerpo e Idempotency-Key

    public function testCreatePostsTheExactContractWithTheIdempotencyKeyHeader(): void
    {
        $this->api->respondWith(self::envelope(self::INGESTED, 201), 201);

        $this->tickets->create(self::newTicket(), self::IDEMPOTENCY_KEY);

        $request = $this->singleRequest();
        self::assertSame('POST', $request['method']);
        self::assertSame('/tickets', $request['uri']);
        self::assertSame(self::EXPECTED_CREATE_JSON, json_encode($request['options']['data']));
        self::assertSame(['Idempotency-Key' => self::IDEMPOTENCY_KEY], $request['options']['headers']);
    }

    public function testCreateWithoutAKeyDoesNotSendTheIdempotencyKeyHeader(): void
    {
        $this->api->respondWith(self::envelope(self::INGESTED, 201), 201);

        $this->tickets->create(self::newTicket());

        $request = $this->singleRequest();
        self::assertSame(self::EXPECTED_CREATE_JSON, json_encode($request['options']['data']));
        self::assertArrayNotHasKey('headers', $request['options']);
    }

    // b. Respuestas de la ingesta

    /**
     * @return array<string, array{int}>
     */
    public function ingestionStatuses(): array
    {
        return ['ticket nuevo' => [201], 'repetición idempotente' => [200]];
    }

    /**
     * @dataProvider ingestionStatuses
     */
    public function testCreateReadsTheNewTicketAndTheIdempotentReplayWithTheSameShape(int $statusCode): void
    {
        $this->api->respondWith(self::envelope(self::INGESTED, $statusCode), $statusCode);

        $response = $this->tickets->create(self::newTicket(), self::IDEMPOTENCY_KEY);

        self::assertSame($statusCode, $response->getStatusCode());
        self::assertTrue($response->isSuccessful());
        self::assertSame(self::INGESTED, $response->getJson()['data']);
        self::assertSame(TicketStatus::PENDING, $response->getJson()['data']['status']);
    }

    public function testADuplicateFolioIs409WithExistingTicketIdInData(): void
    {
        $this->api->respondWith((string) json_encode([
            'data' => ['existingTicketId' => self::TICKET_ID],
            'succeeded' => false,
            'message' => "Sorry, it's not me, it's you.",
            'details' => "Ya hay un ticket activo con esa serie y folio para el emisor: 'data.existingTicketId' es el ticket que lo tiene. Para volver a ingerirlo, cancela ese ticket primero.",
            'httpStatusCode' => 409,
        ]), 409);

        $response = $this->tickets->create(self::newTicket());

        self::assertSame(409, $response->getStatusCode());
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->getJson()['succeeded']);
        self::assertSame(['existingTicketId' => self::TICKET_ID], $response->getJson()['data']);
    }

    public function testATotalMismatchIs422WithExpectedAndComputedTotalInData(): void
    {
        $this->api->respondWith((string) json_encode([
            'data' => ['expectedTotal' => 116.01, 'computedTotal' => 116],
            'succeeded' => false,
            'message' => "Sorry, it's not me, it's you.",
            'details' => "'total' no cuadra al centavo con el total calculado de los conceptos e impuestos: 'data.expectedTotal' es el enviado y 'data.computedTotal' el calculado.",
            'httpStatusCode' => 422,
        ]), 422);

        $response = $this->tickets->create(self::newTicket());

        self::assertSame(422, $response->getStatusCode());
        self::assertSame(['expectedTotal' => 116.01, 'computedTotal' => 116], $response->getJson()['data']);
    }

    // c. Lista con filtros

    public function testGetListSendsEveryFilterRepeatingStatus(): void
    {
        $page = ['items' => [self::SUMMARY], 'pageNumber' => 2, 'totalPages' => 3, 'totalCount' => 21, 'hasPreviousPage' => true, 'hasNextPage' => true];
        $this->api->respondWith(self::envelope($page));

        $response = $this->tickets->getList([
            'status' => [TicketStatus::PENDING, TicketStatus::INVOICED],
            'personId' => self::ISSUER_ID,
            'from' => '2026-09-01',
            'to' => '2026-09-30',
            'pageNumber' => 2,
            'pageSize' => 50,
        ]);

        $request = $this->singleRequest();
        self::assertSame('GET', $request['method']);
        self::assertSame(
            '/tickets?status=pending&status=invoiced&personId=' . self::ISSUER_ID . '&from=2026-09-01&to=2026-09-30&pageNumber=2&pageSize=50',
            $request['uri']
        );
        self::assertSame($page, $response->getJson()['data']);
    }

    public function testGetListOmitsNullFiltersAndSendsNoQueryWithoutFilters(): void
    {
        $this->api->respondWith(self::envelope(['items' => [], 'pageNumber' => 1, 'totalPages' => 0, 'totalCount' => 0, 'hasPreviousPage' => false, 'hasNextPage' => false]));

        $this->tickets->getList();
        $this->tickets->getList(['status' => null, 'personId' => null, 'pageNumber' => 1]);

        $requests = $this->api->getRequests();
        self::assertSame('/tickets', $requests[0]['uri']);
        self::assertSame('/tickets?pageNumber=1', $requests[1]['uri']);
    }

    // d. Detalle

    public function testGetByIdWithoutDetailsAsksWithoutDetails(): void
    {
        $this->api->respondWith(self::envelope(self::SUMMARY));

        $response = $this->tickets->getById(self::TICKET_ID);

        $request = $this->singleRequest();
        self::assertSame('/tickets/' . self::TICKET_ID, $request['uri']);
        self::assertSame(['details' => 'false'], $request['options']['query_params']);
        self::assertSame(self::SUMMARY, $response->getJson()['data']);
    }

    public function testGetByIdWithDetailsReadsItemsAndEvents(): void
    {
        $detail = self::SUMMARY + [
            'items' => [[
                'itemCode' => '50202306', 'itemSku' => 'SKU-1', 'quantity' => 1, 'unitOfMeasurementCode' => 'H87',
                'description' => 'Refresco', 'unitPrice' => 100, 'discount' => 0, 'taxObjectCode' => '02',
                'itemTaxes' => [['taxCode' => '002', 'taxTypeCode' => 'Tasa', 'taxRate' => 0.16, 'taxFlagCode' => 'T']],
            ]],
            'events' => [
                ['type' => 'ingested', 'occurredAt' => '2026-09-01T00:05:00.000', 'actorType' => 'tenant', 'invoiceId' => null],
                ['type' => 'cancelled', 'occurredAt' => '2026-09-01T01:00:00.000', 'actorType' => 'tenant', 'invoiceId' => null],
            ],
        ];
        $this->api->respondWith(self::envelope($detail));

        $response = $this->tickets->getById(self::TICKET_ID, true);

        self::assertSame(['details' => 'true'], $this->singleRequest()['options']['query_params']);
        $ticket = $response->getJson()['data'];
        self::assertSame($detail, $ticket);
        self::assertSame([TicketEventType::INGESTED, TicketEventType::CANCELLED], array_column($ticket['events'], 'type'));
        self::assertSame([TicketActorType::TENANT, TicketActorType::TENANT], array_column($ticket['events'], 'actorType'));
    }

    // e. Código

    public function testGetCodeReadsTheCodeAndTheQrUrl(): void
    {
        $code = ['ticketCode' => 'K7M2Q9X4TB3H', 'qrUrl' => 'https://portal.fiscalapi.com/t/K7M2Q9X4TB3H'];
        $this->api->respondWith(self::envelope($code));

        $response = $this->tickets->getCode(self::TICKET_ID);

        $request = $this->singleRequest();
        self::assertSame('GET', $request['method']);
        self::assertSame('/tickets/' . self::TICKET_ID . '/code', $request['uri']);
        self::assertSame($code, $response->getJson()['data']);
    }

    // f. Cancelación

    public function testCancelUsesDelete(): void
    {
        $this->api->respondWith(self::envelope(true));

        $response = $this->tickets->cancel(self::TICKET_ID);

        $request = $this->singleRequest();
        self::assertSame('DELETE', $request['method']);
        self::assertSame('/tickets/' . self::TICKET_ID, $request['uri']);
        self::assertSame([], $request['options']);
        self::assertTrue($response->getJson()['data']);
    }

    // g. Valores que el SDK no conoce

    public function testAStatusEventTypeAndActorTypeThatTheSdkDoesNotKnowArriveAsText(): void
    {
        $this->api->respondWith(self::envelope(['status' => 'unknownState'] + self::SUMMARY + [
            'items' => [],
            'events' => [['type' => 'unknownEvent', 'occurredAt' => '2026-09-01T00:05:00.000', 'actorType' => 'unknownActor', 'invoiceId' => null]],
        ]));

        $ticket = $this->tickets->getById(self::TICKET_ID, true)->getJson()['data'];

        self::assertSame('unknownState', $ticket['status']);
        self::assertSame('unknownEvent', $ticket['events'][0]['type']);
        self::assertSame('unknownActor', $ticket['events'][0]['actorType']);
    }

    /**
     * @return array<string, array{string}>
     */
    public function missingIds(): array
    {
        return ['vacío' => [''], 'espacios' => [' ']];
    }

    /**
     * @dataProvider missingIds
     */
    public function testEveryCallByIdRejectsAMissingIdWithoutCallingTheApi(string $id): void
    {
        foreach (['getById', 'getCode', 'cancel'] as $method) {
            try {
                $this->tickets->{$method}($id);
                self::fail($method . ' aceptó un id vacío');
            } catch (InvalidArgumentException $exception) {
                self::assertSame('Se requiere el id del ticket', $exception->getMessage());
            }
        }
        self::assertSame([], $this->api->getRequests());
    }
}
