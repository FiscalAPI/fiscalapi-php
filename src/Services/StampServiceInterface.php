<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de timbres (stamps).
 */
interface StampServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de transacciones de timbres
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;
    
    /**
     * Transfiere timbres de una persona a otra.
     *
     * @param array $data Datos de la transferencia (fromPersonId, toPersonId, amount)
     * @return FiscalApiHttpResponseInterface
     */
    public function transferStamps(array $data): FiscalApiHttpResponseInterface;

    /**
     * Retira timbres de una persona.
     *
     * @param array $data Datos del retiro (fromPersonId, toPersonId, amount)
     * @return FiscalApiHttpResponseInterface
     */
    public function withdrawStamps(array $data): FiscalApiHttpResponseInterface;
}
