<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Models\CreditType;
use Fiscalapi\Services\InvoiceService;
use Fiscalapi\Services\PersonService;
use Fiscalapi\Services\StampService;
use PHPUnit\Framework\TestCase;

/**
 * Tolerancia del SDK a respuestas que el API devolverá en las próximas fases (SDK-001).
 *
 * Son pruebas de caracterización: fijan el comportamiento ACTUAL. Los casos son:
 *  1. una transacción de timbres con creditType 3 (créditos de ticket, Fase 1);
 *  2. una persona con el campo nuevo availableTicketBalance (Fase 1);
 *  3. propiedades desconocidas en la envoltura, en data y en objetos anidados;
 *  4. globalInformation en la respuesta de una factura (BE-008).
 *
 * El SDK no tiene modelos: getJson() devuelve el arreglo decodificado tal cual.
 */
final class ResponseToleranceTest extends TestCase
{
    private const TICKET_CREDIT_TYPE = 3;

    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
    }

    // 1. creditType 3

    public function testStampListKeepsCreditType3AsAnIntegerWithoutAConstantSdk013AddsTicket(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('stamps-page-credit-type-3.json'));

        $json = (new StampService($this->api))->list(1, 10)->getJson();

        $this->assertTrue($json['succeeded']);
        $this->assertSame(
            [CreditType::STAMP, CreditType::VALIDATION, self::TICKET_CREDIT_TYPE],
            array_column($json['data']['items'], 'creditType')
        );
        $this->assertNotContains(self::TICKET_CREDIT_TYPE, (new \ReflectionClass(CreditType::class))->getConstants());
        $this->assertSame(['GET /stamps'], $this->api->getRequestedUris());
    }

    public function testStampByIdKeepsCreditType3AsAnInteger(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('stamp-credit-type-3.json'));

        $json = (new StampService($this->api))->get('8d0f6a52-6f1e-4c3a-9a10-000000000103')->getJson();

        $this->assertSame(self::TICKET_CREDIT_TYPE, $json['data']['creditType']);
        $this->assertSame(25, $json['data']['amount']);
    }

    // 2. availableTicketBalance

    public function testPersonKeepsAvailableTicketBalance(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('person-ticket-balance.json'));

        $json = (new PersonService($this->api))->get('4b1c2d3e-0000-4000-8000-000000000001')->getJson();

        $this->assertSame(100, $json['data']['availableBalance']);
        $this->assertSame(50, $json['data']['availableValidationBalance']);
        $this->assertSame(25, $json['data']['availableTicketBalance']);
    }

    // 3. Propiedades desconocidas

    public function testUnknownPropertiesAreKeptOnTheEnvelopeThePageTheDataAndNestedObjects(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('stamps-page-credit-type-3.json'));

        $json = (new StampService($this->api))->list(1, 10)->getJson();

        $ticketTransaction = $json['data']['items'][2];
        $this->assertSame('envelope', $json['unknownEnvelopeField']);
        $this->assertSame(['cursor' => 'c-1'], $json['data']['unknownPageField']);
        $this->assertSame(['code' => 'T', 'enabled' => true], $ticketTransaction['unknownObjectField']);
        $this->assertSame([1, 2, 3], $ticketTransaction['unknownArrayField']);
        $this->assertSame(['nested', 'array'], $ticketTransaction['fromPerson']['unknownNestedField']);
        $this->assertSame('FISCALAPI', $ticketTransaction['fromPerson']['legalName']);
    }

    // 4. globalInformation en la respuesta de una factura (BE-008)

    public function testInvoiceKeepsTheFullGlobalInformation(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('invoice-global-information.json'));

        $json = (new InvoiceService($this->api))->get('3f2a9c1e-0b7d-4e55-9a61-2c8f0e4d7b10')->getJson();

        $this->assertSame(
            ['periodicityCode' => '04', 'monthCode' => '09', 'year' => 2026],
            $json['data']['globalInformation']
        );
        $this->assertSame('EKU9003173C9', $json['data']['issuer']['tin']);
    }

    public function testInvoiceKeepsANullGlobalInformation(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('invoice-global-information-null.json'));

        $json = (new InvoiceService($this->api))->get('3f2a9c1e-0b7d-4e55-9a61-2c8f0e4d7b10')->getJson();

        $this->assertArrayHasKey('globalInformation', $json['data']);
        $this->assertNull($json['data']['globalInformation']);
    }

    public function testInvoiceKeepsAGlobalInformationWithNullMembers(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('invoice-global-information-null-members.json'));

        $json = (new InvoiceService($this->api))->get('3f2a9c1e-0b7d-4e55-9a61-2c8f0e4d7b10')->getJson();

        $this->assertSame(
            ['periodicityCode' => null, 'monthCode' => null, 'year' => null],
            $json['data']['globalInformation']
        );
    }
}
