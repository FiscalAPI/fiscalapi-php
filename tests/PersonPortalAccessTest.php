<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\PersonService;
use PHPUnit\Framework\TestCase;

/**
 * Acceso al portal de una persona: 'loginEnabled' y 'password' son claves del arreglo del integrador. El SDK manda el
 * arreglo tal cual (el cliente HTTP lo codifica con json_encode), así que una clave que no se pone no viaja y un false
 * sí. Los mismos escenarios viven en los SDK de .NET, Node.js, Python y Java.
 */
final class PersonPortalAccessTest extends TestCase
{
    private const PERSON_ID = '78d380fd-1b69-4e3c-8bc0-4f57737f7d5f';

    private const NEW_PERSON = [
        'legalName' => 'KARLA FUENTE NOLASCO',
        'email' => 'persona@example.com',
        'tin' => 'FUNK671228PH6',
        'satTaxRegimeId' => '621',
        'satCfdiUseId' => 'S01',
        'zipCode' => '01160',
    ];

    private FakeFiscalApiHttpClient $api;
    private PersonService $people;

    protected function setUp(): void
    {
        $this->api = new FakeFiscalApiHttpClient();
        $this->api->respondWith((string) json_encode(['data' => ['id' => self::PERSON_ID], 'succeeded' => true, 'message' => '', 'details' => '', 'httpStatusCode' => 200]));
        $this->people = new PersonService($this->api);
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

    public function testCreateWithoutPasswordNorLoginEnabledSendsNeither(): void
    {
        $this->people->create(self::NEW_PERSON);

        $request = $this->singleRequest();
        self::assertSame('POST', $request['method']);
        self::assertSame('/people', $request['uri']);
        self::assertSame(
            '{"legalName":"KARLA FUENTE NOLASCO","email":"persona@example.com","tin":"FUNK671228PH6",'
            . '"satTaxRegimeId":"621","satCfdiUseId":"S01","zipCode":"01160"}',
            json_encode($request['options']['data'])
        );
    }

    public function testCreateWithLoginEnabledSendsItWithThePassword(): void
    {
        $this->people->create(self::NEW_PERSON + ['loginEnabled' => true, 'password' => 'UnaContrasena#1']);

        $data = $this->singleRequest()['options']['data'];
        self::assertTrue($data['loginEnabled']);
        self::assertSame('UnaContrasena#1', $data['password']);
    }

    public function testUpdateWithoutLoginEnabledDoesNotSendIt(): void
    {
        $this->people->update(['id' => self::PERSON_ID, 'legalName' => 'KARLA FUENTE NOLASCO']);

        $request = $this->singleRequest();
        self::assertSame('PUT', $request['method']);
        self::assertSame('/people/' . self::PERSON_ID, $request['uri']);
        self::assertSame(
            '{"id":"' . self::PERSON_ID . '","legalName":"KARLA FUENTE NOLASCO"}',
            json_encode($request['options']['data'])
        );
    }

    public function testUpdateWithLoginEnabledFalseSendsFalse(): void
    {
        $this->people->update(['id' => self::PERSON_ID, 'loginEnabled' => false]);

        self::assertSame(
            '{"id":"' . self::PERSON_ID . '","loginEnabled":false}',
            json_encode($this->singleRequest()['options']['data'])
        );
    }

    public function testGetKeepsTheObsoleteDetailsArgumentWithItsQuery(): void
    {
        $this->people->get(self::PERSON_ID, true);

        $request = $this->singleRequest();
        self::assertSame('GET', $request['method']);
        self::assertSame('/people/' . self::PERSON_ID, $request['uri']);
        self::assertSame(['query_params' => ['details' => 'true']], $request['options']);
    }
}
