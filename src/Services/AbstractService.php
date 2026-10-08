<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Clase abstracta base para implementar servicios de FiscalAPI
 */
abstract class AbstractService extends AbstractImmutableService implements FiscalApiServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function update(array $data): FiscalApiHttpResponseInterface
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException("El campo 'id' es obligatorio para actualizar un recurso");
        }

        return $this->httpClient->put(
            $this->buildResourceUrl($data['id']),
            [
                'data' => $data
            ]
        );
    }
}