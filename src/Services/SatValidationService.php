<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de validaciones SAT.
 */
class SatValidationService implements SatValidationServiceInterface
{
    protected FiscalApiHttpClientInterface $httpClient;
    protected string $resourcePath = 'sat-validations';

    /**
     * Constructor del servicio de validaciones SAT
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
    public function getTypes(): FiscalApiHttpResponseInterface
    {
        return $this->httpClient->get($this->buildResourceUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeById(string $id): FiscalApiHttpResponseInterface
    {
        $this->validateTypeId($id);

        return $this->httpClient->get($this->buildResourceUrl($id));
    }

    /**
     * {@inheritdoc}
     */
    public function getStatuses(string $id): FiscalApiHttpResponseInterface
    {
        $this->validateTypeId($id);

        return $this->httpClient->get($this->buildResourceUrl($id, 'statuses'));
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $data): FiscalApiHttpResponseInterface
    {
        if (!isset($data['validationTypes']) || !is_array($data['validationTypes']) || $data['validationTypes'] === []) {
            throw new InvalidArgumentException('Se requiere al menos un tipo de validación en validationTypes');
        }

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
     * El id del tipo de validación se interpola tal cual: son identificadores con puntos
     * (por ejemplo 'sat.cfdi.status') que la API espera sin codificar.
     *
     * @param string|null $id Id del tipo de validación (opcional)
     * @param string|null $subPath Subruta adicional (opcional)
     * @return string
     */
    protected function buildResourceUrl(?string $id = null, ?string $subPath = null): string
    {
        $url = '/' . $this->resourcePath;

        if ($id !== null) {
            $url .= '/' . $id;
        }

        if ($subPath !== null) {
            $url .= '/' . trim($subPath, '/');
        }

        return $url;
    }

    /**
     * Valida el id del tipo de validación
     *
     * @param string $id Id del tipo de validación
     * @throws InvalidArgumentException
     */
    private function validateTypeId(string $id): void
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException('Se requiere el id del tipo de validación, por ejemplo sat.cfdi.status');
        }
    }
}
