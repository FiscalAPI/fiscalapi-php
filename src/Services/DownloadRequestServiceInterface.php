<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de solicitudes de descarga
 */
interface DownloadRequestServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Lista los xmls descargados para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de objetos Xml
     */
    public function getXmls(string $requestId): FiscalApiHttpResponseInterface;

    /**
     * Lista los meta-items descargados para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de objetos meta-items
     */
    public function getMetadataItems(string $requestId): FiscalApiHttpResponseInterface;

    /**
     * Descarga la lista de paquetes (archivos .zip) de un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de FileResponses
     */
    public function downloadPackage(string $requestId): FiscalApiHttpResponseInterface;

    /**
     * Descarga el archivo crudo de solicitud SAT para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Objeto File response
     */
    public function downloadSatRequest(string $requestId): FiscalApiHttpResponseInterface;

    /**
     * Descarga la respuesta SAT para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Objeto File response
     */
    public function downloadSatResponse(string $requestId): FiscalApiHttpResponseInterface;

    /**
     * Busca solicitudes de descarga creadas en una fecha específica.
     *
     * @param string $createdAt Fecha de creación en formato 'Y-m-d'
     * @return FiscalApiHttpResponseInterface Lista de DownloadRequest
     */
    public function search(string $createdAt): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una lista de solicitudes de descarga
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Obtiene una solicitud de descarga por su ID
     *
     * @param string $id Id de la solicitud de descarga
     * @param bool $details indica si debe recuperar los registros relacionados del registro solicitado. Propiedades expandibles.
     * @return FiscalApiHttpResponseInterface
     */
    public function get(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Crea una nueva solicitud de descarga
     *
     * @param array $data Datos de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Actualiza una solicitud de descarga existente. Debe incluir el key 'id' en el array asociativo.
     *
     * @param array $data Datos a actualizar
     * @return FiscalApiHttpResponseInterface
     */
    public function update(array $data): FiscalApiHttpResponseInterface;

    /**
     * Elimina una solicitud de descarga
     *
     * @param string $id Id de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface
     */
    public function delete(string $id): FiscalApiHttpResponseInterface;
}