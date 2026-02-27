<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de datos de empleado.
 * Opera sobre el sub-recurso /people/{personId}/employee
 */
class EmployeeService implements EmployeeServiceInterface
{
    protected FiscalApiHttpClientInterface $httpClient;
    protected string $resourcePath = 'people';
    protected string $subResourcePath = 'employee';

    /**
     * Constructor del servicio de empleado
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
    public function get(string $personId): FiscalApiHttpResponseInterface
    {
        $this->validatePersonId($personId);

        return $this->httpClient->get($this->buildSubResourceUrl($personId));
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $personId, array $data): FiscalApiHttpResponseInterface
    {
        $this->validatePersonId($personId);

        return $this->httpClient->post(
            $this->buildSubResourceUrl($personId),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $personId, array $data): FiscalApiHttpResponseInterface
    {
        $this->validatePersonId($personId);

        return $this->httpClient->put(
            $this->buildSubResourceUrl($personId),
            [
                'data' => $data
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $personId): FiscalApiHttpResponseInterface
    {
        $this->validatePersonId($personId);

        return $this->httpClient->delete($this->buildSubResourceUrl($personId));
    }

    /**
     * Construye la URL del sub-recurso: /people/{personId}/employee
     *
     * @param string $personId ID de la persona
     * @return string
     */
    protected function buildSubResourceUrl(string $personId): string
    {
        return '/' . $this->resourcePath . '/' . urlencode($personId) . '/' . $this->subResourcePath;
    }

    /**
     * Valida que el ID de la persona no esté vacío
     *
     * @param string $personId ID de la persona
     * @throws InvalidArgumentException
     */
    protected function validatePersonId(string $personId): void
    {
        if (empty(trim($personId))) {
            throw new InvalidArgumentException('El ID de la persona es obligatorio');
        }
    }
}
