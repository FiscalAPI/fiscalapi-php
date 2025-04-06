<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de archivos fiscales
 */
interface TaxFileServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de archivos fiscales
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 20): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un archivo fiscal por su ID
     *
     * @param string $id Id del archivo fiscal
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id): FiscalApiHttpResponseInterface;

    /**
     * Crea un nuevo archivo fiscal
     *
     * @param array $data Datos del archivo fiscal
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza un archivo fiscal existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina un archivo fiscal
     *
     * @param string $id Id del archivo fiscal
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el último par de ids de certificados válidos y vigente de una persona. 
     * Es decir sus certificados por defecto (ids)
     *
     * @param string $personId Id de la persona propietaria de los certificados
     * @return FiscalApiHttpResponseInterface
     */
    public function getDefaultReferences(string $personId): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el último par de certificados válidos y vigente de una persona. 
     * Es decir sus certificados por defecto
     *
     * @param string $personId Id de la persona dueña de los certificados
     * @return FiscalApiHttpResponseInterface
     */
    public function getDefaultValues(string $personId): FiscalApiHttpResponseInterface;
}