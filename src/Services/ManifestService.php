<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de manifiestos.
 */
class ManifestService implements ManifestServiceInterface
{
    protected FiscalApiHttpClientInterface $httpClient;
    protected string $resourcePath = 'manifests';

    /**
     * Constructor del servicio de manifiestos
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function sign(array $data): FiscalApiHttpResponseInterface
    {
        $this->validateSignRequest($data);

        return $this->httpClient->post(
            $this->buildResourceUrl(),
            [
                'data' => $data
            ]
        );
    }

    /**
     * Construye la URL del recurso.
     *
     * @return string
     */
    protected function buildResourceUrl(): string
    {
        return '/' . $this->resourcePath;
    }

    /**
     * Valida la solicitud de firma
     *
     * @param array $data Datos de la solicitud de firma
     * @throws InvalidArgumentException
     */
    private function validateSignRequest(array $data): void
    {
        $requiredKeys = [
            'base64Cer' => 'Se requiere el certificado de la e.firma (FIEL) en base64 en base64Cer',
            'base64Key' => 'Se requiere la llave privada de la e.firma (FIEL) en base64 en base64Key',
            'password' => 'Se requiere la contraseña de la llave privada en password',
        ];

        foreach ($requiredKeys as $key => $message) {
            if (!isset($data[$key]) || !is_string($data[$key]) || trim($data[$key]) === '') {
                throw new InvalidArgumentException($message);
            }
        }
    }
}
