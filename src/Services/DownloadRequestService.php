<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de solicitudes de descarga
 */
class DownloadRequestService extends AbstractService implements DownloadRequestServiceInterface
{
    /**
     * Constructor del servicio de solicitudes de descarga
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'download-requests');
    }

    /**
     * Lista los xmls descargados para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de objetos Xml
     */
    public function getXmls(string $requestId): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/xmls', $requestId);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * Lista los meta-items descargados para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de objetos meta-items
     */
    public function getMetadataItems(string $requestId): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/meta-items', $requestId);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * Descarga la lista de paquetes (archivos .zip) de un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Lista de FileResponses
     */
    public function downloadPackage(string $requestId): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/package', $requestId);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * Descarga el archivo crudo de solicitud SAT para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Objeto File response
     */
    public function downloadSatRequest(string $requestId): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/raw-request', $requestId);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * Descarga la respuesta SAT para un requestId.
     *
     * @param string $requestId ID de la solicitud de descarga
     * @return FiscalApiHttpResponseInterface Objeto File response
     */
    public function downloadSatResponse(string $requestId): FiscalApiHttpResponseInterface
    {
        $path = sprintf('%s/raw-response', $requestId);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }

    /**
     * Busca solicitudes de descarga creadas en una fecha específica.
     *
     * @param string $createdAt Fecha de creación en formato 'Y-m-d'
     * @return FiscalApiHttpResponseInterface Lista de DownloadRequest
     */
    public function search(string $createdAt): FiscalApiHttpResponseInterface
    {
        $path = sprintf('search?createdAt=%s', $createdAt);
        return $this->httpClient->get($this->buildResourceUrl(null, $path));
    }
}