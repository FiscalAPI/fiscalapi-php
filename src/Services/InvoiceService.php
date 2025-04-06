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
    private const INCOME_ENDPOINT = 'income';
    private const CREDIT_NOTE_ENDPOINT = 'credit-note';
    private const PAYMENT_ENDPOINT = 'payment';

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
        if (!isset($data['typeCode'])) {
            throw new InvalidArgumentException('El campo typeCode es obligatorio para crear una factura');
        }

        $endpoint = '';
        switch ($data['typeCode']) {
            case 'I':
                $endpoint = self::INCOME_ENDPOINT;
                break;
            case 'E':
                $endpoint = self::CREDIT_NOTE_ENDPOINT;
                break;
            case 'P':
                $endpoint = self::PAYMENT_ENDPOINT;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Tipo de factura no soportado: %s', $data['typeCode']));
        }

        return $this->httpClient->post(
            $this->buildResourceUrl($endpoint),
            [
                'data' => $data
            ]
        );
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