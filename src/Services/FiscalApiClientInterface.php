<?php
declare(strict_types=1);
namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;


interface FiscalApiClientInterface
{
    /**
     * Obtiene el servicio de productos
     *
     * @return ProductServiceInterface
     */
    public function getProductService(): ProductServiceInterface;

    /**
     * Obtiene el servicio de personas
     *
     * @return PersonServiceInterface
     */
    public function getPersonService(): PersonServiceInterface;

    /**
     * Obtiene el servicio de API Keys
     *
     * @return ApiKeyServiceInterface
     */
    public function getApiKeyService(): ApiKeyServiceInterface;

    /**
     * Obtiene el servicio de catálogos
     *
     * @return CatalogServiceInterface
     */
    public function getCatalogService(): CatalogServiceInterface;

    /**
     * Obtiene el servicio de archivos fiscales
     *
     * @return TaxFileServiceInterface
     */
    public function getTaxFileService(): TaxFileServiceInterface;

    /**
     * Obtiene el servicio de facturas
     * 
     * @return InvoiceServiceInterface
     */
    public function getInvoiceService(): InvoiceServiceInterface;



    /**
     * Obtiene el servicio de catalogos de descarga masiva.
     *
     * @return DownloadCatalogServiceInterface
     */
    public function getDownloadCatalogService(): DownloadCatalogServiceInterface;


    /**
     * Obtiene el servicio de reglas de descarga masiva.
     *
     * @return DownloadRuleServiceInterface
     */
    public function getDownloadRuleService(): DownloadRuleServiceInterface;

    /**
     * Obtiene el servicio de solicitudes de descarga masiva.
     *
     * @return DownloadRequestServiceInterface
     */
    public function getDownloadRequestService(): DownloadRequestServiceInterface;



    /**
     * Obtiene el cliente HTTP subyacente.
     *
     * @return FiscalApiHttpClientInterface
     */
    public function getHttpClient(): FiscalApiHttpClientInterface;
}