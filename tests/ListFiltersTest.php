<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Models\InvoiceListOrder;
use Fiscalapi\Services\InvoiceService;
use Fiscalapi\Services\PersonService;
use Fiscalapi\Services\ProductService;
use PHPUnit\Framework\TestCase;

/**
 * Filtros de las listas de facturas, personas y productos (GET /api/v4/invoices, /people y /products), con el patrón
 * de getTicketService()->getList(): cada filtro asignado viaja en la query string con su clave camelCase, uno en null
 * no se envía y sin filtros se pide la ruta sin query. list($pageNumber, $pageSize) sigue igual. Los mismos escenarios
 * viven en los SDK de .NET, Node.js, Python y Java.
 */
final class ListFiltersTest extends TestCase
{
    private const PAGE = ['items' => [], 'pageNumber' => 1, 'totalPages' => 0, 'totalCount' => 0, 'hasPreviousPage' => false, 'hasNextPage' => false];

    private FakeFiscalApiHttpClient $api;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
        $this->api->respondWith((string) json_encode(['data' => self::PAGE, 'succeeded' => true, 'message' => '', 'details' => '', 'httpStatusCode' => 200]));
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

    /**
     * @return array<string, array{0: callable(FakeFiscalApiHttpClient): object, 1: string}>
     */
    public function services(): array
    {
        return [
            'facturas' => [static function (FakeFiscalApiHttpClient $api) { return new InvoiceService($api); }, '/invoices'],
            'personas' => [static function (FakeFiscalApiHttpClient $api) { return new PersonService($api); }, '/people'],
            'productos' => [static function (FakeFiscalApiHttpClient $api) { return new ProductService($api); }, '/products'],
        ];
    }

    public function testInvoiceGetListSendsEveryFilterWithItsCamelCaseKey(): void
    {
        $response = (new InvoiceService($this->api))->getList([
            'from' => '2026-10-01',
            'to' => '2026-10-31',
            'issuerTin' => 'EKU9003173C9',
            'recipientTin' => 'FUNK671228PH6',
            'typeCode' => 'I',
            'statusId' => 'V',
            'series' => 'A',
            'number' => '1001',
            'uuid' => '5b6c7d8e-0000-4000-8000-0000000000c3',
            'order' => InvoiceListOrder::DESC,
            'pageNumber' => 2,
            'pageSize' => 50,
        ]);

        $request = $this->singleRequest();
        self::assertSame('GET', $request['method']);
        self::assertSame(
            '/invoices?from=2026-10-01&to=2026-10-31&issuerTin=EKU9003173C9&recipientTin=FUNK671228PH6&typeCode=I'
            . '&statusId=V&series=A&number=1001&uuid=5b6c7d8e-0000-4000-8000-0000000000c3&order=desc&pageNumber=2&pageSize=50',
            $request['uri']
        );
        self::assertSame([], $request['options']);
        self::assertSame(self::PAGE, $response->getJson()['data']);
    }

    public function testInvoiceGetListOmitsNullFilters(): void
    {
        (new InvoiceService($this->api))->getList([
            'from' => null,
            'to' => null,
            'issuerTin' => null,
            'typeCode' => 'P',
            'uuid' => null,
            'order' => InvoiceListOrder::DESC,
            'pageSize' => null,
        ]);

        self::assertSame('/invoices?typeCode=P&order=desc', $this->singleRequest()['uri']);
    }

    public function testInvoiceListOrderHasTheValuesOfTheApi(): void
    {
        self::assertSame('asc', InvoiceListOrder::ASC);
        self::assertSame('desc', InvoiceListOrder::DESC);
    }

    public function testPersonGetListSendsSearchEscapedAndUserTypeId(): void
    {
        (new PersonService($this->api))->getList([
            'search' => 'Escuela Kemper & Hijos',
            'userTypeId' => 'C',
            'pageNumber' => 1,
            'pageSize' => 20,
        ]);

        self::assertSame(
            '/people?search=Escuela%20Kemper%20%26%20Hijos&userTypeId=C&pageNumber=1&pageSize=20',
            $this->singleRequest()['uri']
        );
    }

    public function testPersonGetListOmitsNullFilters(): void
    {
        (new PersonService($this->api))->getList(['search' => null, 'userTypeId' => 'U', 'pageNumber' => null]);

        self::assertSame('/people?userTypeId=U', $this->singleRequest()['uri']);
    }

    public function testProductGetListSendsSearchAndPage(): void
    {
        (new ProductService($this->api))->getList(['search' => '50211503', 'pageNumber' => 3, 'pageSize' => 10]);

        self::assertSame('/products?search=50211503&pageNumber=3&pageSize=10', $this->singleRequest()['uri']);
    }

    public function testProductGetListOmitsNullFilters(): void
    {
        (new ProductService($this->api))->getList(['search' => null, 'pageNumber' => 2, 'pageSize' => null]);

        self::assertSame('/products?pageNumber=2', $this->singleRequest()['uri']);
    }

    /**
     * @dataProvider services
     */
    public function testGetListWithoutFiltersAsksTheListWithoutQuery(callable $newService, string $path): void
    {
        $service = $newService($this->api);

        $service->getList();
        $service->getList(['search' => null, 'pageNumber' => null]);

        $requests = $this->api->getRequests();
        self::assertCount(2, $requests);
        self::assertSame($path, $requests[0]['uri']);
        self::assertSame($path, $requests[1]['uri']);
        self::assertSame([], $requests[0]['options']);
    }

    /**
     * @dataProvider services
     */
    public function testListWithPageNumberAndPageSizeIsUnchanged(callable $newService, string $path): void
    {
        $newService($this->api)->list(2, 5);

        $request = $this->singleRequest();
        self::assertSame('GET', $request['method']);
        self::assertSame($path, $request['uri']);
        self::assertSame(['query_params' => ['pageNumber' => 2, 'pageSize' => 5]], $request['options']);
    }
}
