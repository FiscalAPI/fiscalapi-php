<?php
declare(strict_types=1);

namespace Fiscalapi\Tests;

use Fiscalapi\Services\InvoiceService;
use PHPUnit\Framework\TestCase;

/** Cancelar factura con rechazo del SAT: 422, 'succeeded' en false y el acuse en 'data' del JSON. */
final class CancelInvoiceRejectionTest extends TestCase
{
    private const INVOICE_ID = '21c39425-9d0a-449b-a7ba-49335b85b062';
    private const INVOICE_UUID = 'de841944-bd4f-4bb8-adfe-2a2282787c62';

    public function testASatRejectionIs422WithTheSatDetailsAndTheAcknowledgementInData(): void
    {
        $api = new FakeFiscalApiHttpClient();
        $api->respondWith((string) json_encode([
            'data' => [
                'base64CancellationAcknowledgement' => 'PD94bWwgdmVyc2lvbj0iMS4wIj8+...',
                'invoiceUuids' => [self::INVOICE_UUID => '203'],
            ],
            'succeeded' => false,
            'message' => "Sorry, it's not me, it's you.",
            'details' => 'SAT 203: el folio fiscal no corresponde al emisor de la solicitud de cancelación.',
            'httpStatusCode' => 422,
        ], JSON_UNESCAPED_UNICODE), 422);

        $response = (new InvoiceService($api))->cancel(['id' => self::INVOICE_ID, 'cancellationReasonCode' => '02']);

        $body = $response->getJson();
        self::assertSame(422, $response->getStatusCode());
        self::assertFalse($body['succeeded']);
        self::assertSame("Sorry, it's not me, it's you.", $body['message']);
        self::assertSame(
            'SAT 203: el folio fiscal no corresponde al emisor de la solicitud de cancelación.',
            $body['details']
        );
        self::assertSame('PD94bWwgdmVyc2lvbj0iMS4wIj8+...', $body['data']['base64CancellationAcknowledgement']);
        self::assertSame([self::INVOICE_UUID => '203'], $body['data']['invoiceUuids']);
        self::assertSame('DELETE', $api->getRequests()[0]['method']);
    }
}
