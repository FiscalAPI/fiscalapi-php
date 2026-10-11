<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Models\CreditType;
use Fiscalapi\Services\InvoiceService;
use Fiscalapi\Services\PersonService;
use Fiscalapi\Services\StampService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tolerancia del SDK a respuestas del API con valores y campos que el SDK documenta o todavía no conoce.
 *
 * Los casos son:
 *  1. transacciones con creditType 3 (CreditType::TICKET): lectura y transferencia;
 *  2. una persona con el saldo de créditos de ticket en balances;
 *  3. propiedades desconocidas en la envoltura, en data y en objetos anidados;
 *  4. globalInformation en la respuesta de una factura.
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

    // 1. creditType 3 (TICKET)

    public function testStampListReadsCreditType3AsTicket(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('stamps-page-credit-type-3.json'));

        $json = (new StampService($this->api))->list(1, 10)->getJson();

        $this->assertTrue($json['succeeded']);
        $this->assertSame(
            [CreditType::STAMP, CreditType::VALIDATION, CreditType::TICKET],
            array_column($json['data']['items'], 'creditType')
        );
        $this->assertSame(self::TICKET_CREDIT_TYPE, CreditType::TICKET);
        $this->assertSame(['GET /stamps'], $this->api->getRequestedUris());
    }

    public function testStampByIdReadsCreditType3AsTicket(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('stamp-credit-type-3.json'));

        $json = (new StampService($this->api))->get('8d0f6a52-6f1e-4c3a-9a10-000000000103')->getJson();

        $this->assertSame(CreditType::TICKET, $json['data']['creditType']);
        $this->assertSame(25, $json['data']['amount']);
    }

    public function testTransferStampsWithTicketPostsCreditType3(): void
    {
        $this->api->respondWith('{"data":true,"succeeded":true,"message":"","details":"","httpStatusCode":200}');
        $transfer = [
            'fromPersonId' => '1',
            'toPersonId' => '4b1c2d3e-0000-4000-8000-000000000001',
            'amount' => 25,
            'creditType' => CreditType::TICKET,
        ];

        $json = (new StampService($this->api))->transferStamps($transfer)->getJson();

        $this->assertTrue($json['succeeded']);
        $requests = $this->api->getRequests();
        $this->assertCount(1, $requests);
        $this->assertSame('POST', $requests[0]['method']);
        $this->assertSame('/stamps', $requests[0]['uri']);
        $this->assertSame(self::TICKET_CREDIT_TYPE, $requests[0]['options']['data']['creditType']);
    }

    public function testTransferStampsWithAnUnknownCreditTypeThrowsWithoutCallingTheApi(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^' . preg_quote('creditType debe ser 1 (Stamp), 2 (Validation) o 3 (Ticket).', '/') . '$/');

        try {
            (new StampService($this->api))->transferStamps([
                'fromPersonId' => '1',
                'toPersonId' => '4b1c2d3e-0000-4000-8000-000000000001',
                'amount' => 25,
                'creditType' => 99,
            ]);
        } finally {
            $this->assertSame([], $this->api->getRequests());
        }
    }

    // 2. Saldo de créditos de ticket en balances

    public function testPersonReadsTheTicketBalanceFromBalances(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('person-ticket-balance.json'));

        $json = (new PersonService($this->api))->get('4b1c2d3e-0000-4000-8000-000000000001')->getJson();

        $this->assertSame(100, $json['data']['availableBalance']);
        $this->assertSame(50, $json['data']['availableValidationBalance']);
        $this->assertSame(
            [CreditType::STAMP => 100, CreditType::VALIDATION => 50, CreditType::TICKET => 25],
            array_column($json['data']['balances'], 'available', 'creditType')
        );
        $this->assertArrayNotHasKey('availableTicketBalance', $json['data']);
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

    // 4. globalInformation en la respuesta de una factura

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
