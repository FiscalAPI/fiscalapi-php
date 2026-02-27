<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de datos de empleador (patrón).
 * Gestiona los datos de empleador asociados a una persona (sub-recurso de people).
 */
interface EmployerServiceInterface
{
    /**
     * Obtiene los datos de empleador de una persona
     *
     * @param string $personId ID de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $personId): FiscalApiHttpResponseInterface;

    /**
     * Crea los datos de empleador para una persona
     *
     * @param string $personId ID de la persona
     * @param array $data Datos del empleador (employerRegistration, satFundSourceId, etc.)
     * @return FiscalApiHttpResponseInterface
     */
    public function create(string $personId, array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza los datos de empleador de una persona
     *
     * @param string $personId ID de la persona
     * @param array $data Datos del empleador a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(string $personId, array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina los datos de empleador de una persona
     *
     * @param string $personId ID de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $personId): FiscalApiHttpResponseInterface;
}
