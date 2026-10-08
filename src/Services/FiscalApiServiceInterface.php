<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz base para todos los servicios de FiscalAPI
 */
interface FiscalApiServiceInterface extends ImmutableFiscalApiServiceInterface
{
    /**
     * Actualiza un recurso existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;
}