<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\AbstractImmutableService;
use Fiscalapi\Services\FiscalApiServiceInterface;
use Fiscalapi\Services\ImmutableFiscalApiServiceInterface;
use Fiscalapi\Services\PersonService;
use Fiscalapi\Services\TaxFileService;
use Fiscalapi\Services\TaxFileServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * Cambios de código del SDK en el ciclo de deuda técnica 2 (Fase III, C22). Los mismos escenarios viven en los SDK de
 * .NET, Node.js, Python y Java (DEC-136), cuando el cambio aplica a cada uno:
 *  1. persona (SDK-043, SDK-044): no aplica, el SDK de PHP no tiene modelos;
 *  2. certificados: tin opcional al subir (SDK-045) y fileType 2 y 3 de la FIEL (SDK-058); el SDK envía el arreglo
 *     tal cual;
 *  3. certificados sin update(): el API retiró PUT tax-files (SDK-051); los servicios que sí actualizan lo conservan.
 */
final class CycleContractTest extends TestCase
{
    private const PERSON_ID = '4b1c2d3e-0000-4000-8000-000000000002';
    private const TAX_FILE_ID = '7a1b2c3d-0000-4000-8000-0000000000f1';
    private const BASE64_CER = 'MIIFsDCCA5igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MTYwDQYJKoZIhvcNAQELBQAw';

    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
    }

    private static function envelope($data): string
    {
        return (string) json_encode(['data' => $data, 'succeeded' => true, 'message' => '', 'details' => '', 'httpStatusCode' => 200]);
    }

    // 2. Certificados: tin opcional (SDK-045) y fileType de la FIEL (SDK-058)

    /**
     * @return array<string, array{int}>
     */
    public function fielFileTypes(): array
    {
        return ['certificado FIEL' => [2], 'llave privada FIEL' => [3]];
    }

    /**
     * @dataProvider fielFileTypes
     */
    public function testTaxFileCreateWithoutTinSendsTheFileTypeAsANumberAndNoTin(int $fileType): void
    {
        $this->api->respondWith(self::envelope(['id' => self::TAX_FILE_ID, 'personId' => self::PERSON_ID, 'tin' => 'EKU9003173C9', 'fileType' => $fileType]));
        $taxFile = [
            'personId' => self::PERSON_ID,
            'base64File' => self::BASE64_CER,
            'fileType' => $fileType,
            'password' => '12345678a',
        ];

        $json = (new TaxFileService($this->api))->create($taxFile)->getJson();

        $this->assertTrue($json['succeeded']);
        $this->assertSame('EKU9003173C9', $json['data']['tin']);
        $requests = $this->api->getRequests();
        $this->assertCount(1, $requests);
        $this->assertSame('POST', $requests[0]['method']);
        $this->assertSame('/tax-files', $requests[0]['uri']);
        $this->assertSame($taxFile, $requests[0]['options']['data']);
        $this->assertArrayNotHasKey('tin', $requests[0]['options']['data']);
    }

    // 3. Certificados sin update() (SDK-051)

    public function testTaxFileServiceHasNoUpdate(): void
    {
        $this->assertFalse(method_exists(TaxFileService::class, 'update'));
        $this->assertFalse(method_exists(TaxFileServiceInterface::class, 'update'));
        $this->assertFalse(is_subclass_of(TaxFileService::class, FiscalApiServiceInterface::class));
        $this->assertTrue(is_subclass_of(TaxFileService::class, ImmutableFiscalApiServiceInterface::class));
        $this->assertTrue(is_subclass_of(TaxFileService::class, AbstractImmutableService::class));
    }

    public function testPersonServiceKeepsUpdateAfterTheImmutableServiceSplit(): void
    {
        $this->api->respondWith(self::envelope(['id' => self::PERSON_ID]));
        $person = ['id' => self::PERSON_ID, 'legalName' => 'ESCUELA KEMPER URGATE'];

        $json = (new PersonService($this->api))->update($person)->getJson();

        $this->assertTrue($json['succeeded']);
        $requests = $this->api->getRequests();
        $this->assertCount(1, $requests);
        $this->assertSame('PUT', $requests[0]['method']);
        $this->assertSame('/people/' . self::PERSON_ID, $requests[0]['uri']);
        $this->assertSame($person, $requests[0]['options']['data']);
    }

    public function testTaxFileDeleteSendsDeleteToTheTaxFile(): void
    {
        $this->api->respondWith(self::envelope(true));

        $json = (new TaxFileService($this->api))->delete(self::TAX_FILE_ID)->getJson();

        $this->assertTrue($json['succeeded']);
        $this->assertTrue($json['data']);
        $this->assertSame(['DELETE /tax-files/' . self::TAX_FILE_ID], $this->api->getRequestedUris());
    }
}
