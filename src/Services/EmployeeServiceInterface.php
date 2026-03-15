<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de datos de empleado.
 * Gestiona los datos de empleado asociados a una persona (sub-recurso de people).
 */
interface EmployeeServiceInterface
{
    /**
     * Obtiene los datos de empleado de una persona
     *
     * @param string $personId ID de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $personId): FiscalApiHttpResponseInterface;

    /**
     * Crea los datos de empleado para una persona
     *
     * @param string $personId ID de la persona
     * @param array $data Datos del empleado (curp, socialSecurityNumber, satContractTypeId, etc.)
     * @return FiscalApiHttpResponseInterface
     */
    public function create(string $personId, array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza los datos de empleado de una persona
     *
     * @param string $personId ID de la persona
     * @param array $data Datos del empleado a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(string $personId, array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina los datos de empleado de una persona
     *
     * @param string $personId ID de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $personId): FiscalApiHttpResponseInterface;
}
