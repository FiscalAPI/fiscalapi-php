<?php

declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de personas
 */
interface PersonServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene el sub-servicio de datos de empleado.
     *
     * @return EmployeeServiceInterface
     */
    public function getEmployeeService(): EmployeeServiceInterface;

    /**
     * Obtiene el sub-servicio de datos de empleador (patrón).
     *
     * @return EmployerServiceInterface
     */
    public function getEmployerService(): EmployerServiceInterface;

    /**
     * Obtiene una lista de personas
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Lista las personas del tenant con los filtros asignados. GET /api/v4/people
     *
     * Sin filtros, la lista de siempre (primera página). list($pageNumber, $pageSize) sigue igual.
     *
     * @param array $filters 'search' (2 a 100 caracteres, sin distinguir mayúsculas: razón social, RFC o correo),
     *                       'userTypeId' (T tenant, U usuario o C cliente, en mayúsculas), 'pageNumber' (desde 1) y
     *                       'pageSize' (1 a 50). Cada clave viaja tal cual en la query string; los valores null no se
     *                       envían.
     * @return FiscalApiHttpResponseInterface Página de personas
     */
    public function getList(array $filters = []): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una persona por su ID. GET /api/v4/people/{id}
     *
     * @param string $id Id de la persona
     * @param bool $details Obsoleto y sin efecto en personas: el API responde la misma persona con cualquier valor. Se
     *                      conserva para no romper la firma; llama get($id). En facturas sigue teniendo efecto.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea una nueva persona. POST /api/v4/people
     *
     * El arreglo viaja tal cual: una clave que no se pone no se envía. 'loginEnabled' (acceso al portal; no afecta a la
     * API key) es opcional: sin ella o en false, la persona nace sin acceso. 'password' es opcional y solo obligatoria
     * con 'loginEnabled' en true. El API nunca devuelve la contraseña.
     *
     * @param array $data Datos de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza una persona existente. Debe incluir el key 'id' en el array asociativo. PUT /api/v4/people/{id}
     *
     * El arreglo viaja tal cual: sin 'loginEnabled' no se envía y el acceso al portal no cambia; true lo da (la persona
     * debe tener contraseña o recibirla en 'password') y false lo quita. Solo lo cambia el owner del tenant, y el owner
     * siempre lo tiene. Sin 'password' o vacía, se conserva la actual.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina una persona
     *
     * @param string $id Id de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;

    /**
     * Lista los certificados (CSD y FIEL) de una persona, solo metadatos, sin el archivo ni la contraseña.
     *
     * @param string $personId Id de la persona
     * @return FiscalApiHttpResponseInterface
     */
    public function getTaxFiles(string $personId): FiscalApiHttpResponseInterface;
}