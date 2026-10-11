<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de facturas
 */
class InvoiceService extends AbstractService implements InvoiceServiceInterface
{
    /**
     * Constructor del servicio de facturas
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'invoices');
    }


    
    /**
     * {@inheritdoc}
     */
    public function create(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->post(
            $this->buildResourceUrl(),
            [
                'data' => self::withoutNullTicketIds($data)
            ]
        );
    }

    /**
     * Quita 'ticketId' de los conceptos donde es null, para que un concepto sin ticket viaje igual que antes de Auto CFDI
     * (sin "ticketId": null). El resto del cuerpo se envía tal cual.
     *
     * @param array $data Datos de la factura
     * @return array
     */
    private static function withoutNullTicketIds(array $data): array
    {
        if (!isset($data['items']) || !is_array($data['items'])) {
            return $data;
        }

        foreach ($data['items'] as $index => $item) {
            if (is_array($item) && array_key_exists('ticketId', $item) && $item['ticketId'] === null) {
                unset($data['items'][$index]['ticketId']);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->delete(
            $this->buildResourceUrl(),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPdf(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->post(
            $this->buildResourceUrl('pdf'),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getXml(string $id): FiscalApiHttpResponseInterface
    {
        if (empty(trim($id))) {
            throw new InvalidArgumentException('El ID no puede estar vacío');
        }

        return $this->httpClient->get(
            $this->buildResourceUrl($id , 'xml')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->post(
            $this->buildResourceUrl('send'),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(array $data): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->post(
            $this->buildResourceUrl('status'),
            [
                'data' => $data
            ]
        );
    }
}