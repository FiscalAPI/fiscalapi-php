<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Models\CreditType;
use Fiscalapi\Services\FiscalApiClient;
use Fiscalapi\Services\MeService;
use Fiscalapi\Services\MeServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * GET /api/v4/me: la persona de la credencial con sus features y saldos, con las claves del back. Los mismos escenarios
 * viven en los SDK de .NET, Node.js, Python y Java.
 */
final class MeServiceTest extends TestCase
{
    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('me.json'));
    }

    public function testTheClientExposesTheMeService(): void
    {
        $client = new FiscalApiClient(new FiscalApiSettings('https://sdk-tests.fiscalapi.invalid', 'sk_test_sdk', 'tenant-sdk'));

        self::assertInstanceOf(MeServiceInterface::class, $client->getMeService());
        self::assertSame($client->getMeService(), $client->getMeService());
    }

    public function testGetCallsMeWithoutBodyNorQuery(): void
    {
        (new MeService($this->api))->get();

        $requests = $this->api->getRequests();
        self::assertCount(1, $requests);
        self::assertSame('GET', $requests[0]['method']);
        self::assertSame('/me', $requests[0]['uri']);
        self::assertSame([], $requests[0]['options']);
    }

    public function testGetReturnsThePersonWithItsFeaturesAndBalances(): void
    {
        $response = (new MeService($this->api))->get();

        self::assertSame(200, $response->getStatusCode());
        $json = $response->getJson();
        self::assertTrue($json['succeeded']);

        $me = $json['data'];
        self::assertSame('78d380fd-1b69-4e3c-8bc0-4f57737f7d5f', $me['id']);
        self::assertSame('KARLA FUENTE NOLASCO', $me['legalName']);
        self::assertSame('FUNK671228PH6', $me['tin']);
        self::assertSame('persona@example.com', $me['email']);
        self::assertSame('102e5f13-e114-41dd-bea7-507fce177281', $me['tenantId']);
        self::assertSame('Test', $me['environment']);
        self::assertTrue($me['isOwner']);

        $features = $me['features'];
        self::assertTrue($features['isOwner']);
        self::assertSame(['Owner'], $features['roles']);
        self::assertSame(['Invoices.Read', 'Invoices.Issue'], $features['permissions']);
        self::assertSame(['auto_cfdi'], $features['denied']['Tickets.Create']['missingFeatures']);
        self::assertSame(['seats' => 2], $features['features']['descarga_cfdi']['values']);
        self::assertSame(['status' => 'active', 'isAvailable' => true], $features['subscription']);
        self::assertSame([['featureCode' => 'descarga_cfdi', 'units' => 1, 'status' => 'Active']], $features['seatAssignments']);

        self::assertSame(
            [['creditType' => CreditType::STAMP, 'available' => 7], ['creditType' => CreditType::VALIDATION, 'available' => 3]],
            $me['balances']
        );
    }
}
