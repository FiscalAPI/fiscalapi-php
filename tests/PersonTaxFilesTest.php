<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\PersonService;
use PHPUnit\Framework\TestCase;

final class PersonTaxFilesTest extends TestCase
{
    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
    }

    public function testGetTaxFilesCallsTheTaxFilesSubresourceOfThePerson(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('person-tax-files.json'));

        (new PersonService($this->api))->getTaxFiles('p1');

        $requests = $this->api->getRequests();
        $this->assertCount(1, $requests);
        $this->assertSame('GET', $requests[0]['method']);
        $this->assertSame('/people/p1/tax-files', $requests[0]['uri']);
        $this->assertSame([], $requests[0]['options']);
    }

    public function testGetTaxFilesReturnsTheMetadataListWithoutFileOrPassword(): void
    {
        $this->api->respondWith(FakeFiscalApiHttpClient::readFixture('person-tax-files.json'));

        $json = (new PersonService($this->api))->getTaxFiles('p1')->getJson();

        $this->assertTrue($json['succeeded']);
        $this->assertSame(200, $json['httpStatusCode']);
        $this->assertCount(2, $json['data']);

        $csd = $json['data'][0];
        $this->assertSame('a1', $csd['id']);
        $this->assertSame('p1', $csd['personId']);
        $this->assertSame('EKU9003173C9', $csd['tin']);
        $this->assertSame(0, $csd['fileType']);
        $this->assertSame('2023-05-18T11:43:51', $csd['validFrom']);
        $this->assertSame('2027-05-18T11:43:51', $csd['validTo']);
        $this->assertSame(1, $csd['sequence']);

        $csdKey = $json['data'][1];
        $this->assertSame('a2', $csdKey['id']);
        $this->assertSame(1, $csdKey['fileType']);

        $this->assertArrayNotHasKey('base64File', $csd);
        $this->assertArrayNotHasKey('password', $csd);
        $this->assertArrayNotHasKey('createdAt', $csd);
        $this->assertArrayNotHasKey('updatedAt', $csd);
    }
}
