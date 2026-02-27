<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de timbres (stamps).
 */
class StampService extends AbstractService implements StampServiceInterface
{
    /**
     * Constructor del servicio de timbres
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'stamps');
    }

    /**
     * {@inheritdoc}
     */
    public function transferStamps(array $data): FiscalApiHttpResponseInterface
    {
        $this->validateStampTransaction($data);

        return $this->httpClient->post(
            $this->buildResourceUrl(),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withdrawStamps(array $data): FiscalApiHttpResponseInterface
    {
        $this->validateStampTransaction($data);

        return $this->httpClient->post(
            $this->buildResourceUrl(),
            [
                'data' => $data
            ]
        );
    }

    /**
     * Valida los datos de la transacción de timbres
     *
     * @param array $data Datos de la transacción
     * @throws InvalidArgumentException
     */
    private function validateStampTransaction(array $data): void
    {
        if (!isset($data['fromPersonId']) || empty(trim($data['fromPersonId']))) {
            throw new InvalidArgumentException('Se requiere el ID de la persona de origen para la transferencia de timbres');
        }

        if (!isset($data['toPersonId']) || empty(trim($data['toPersonId']))) {
            throw new InvalidArgumentException('Se requiere el ID de la persona de destino para la transferencia de timbres');
        }

        if (!isset($data['amount']) || $data['amount'] <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que cero');
        }
    }
}
