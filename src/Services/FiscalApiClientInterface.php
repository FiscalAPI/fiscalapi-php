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
     * Obtiene el cliente HTTP subyacente.
     *
     * @return FiscalApiHttpClientInterface
     */
    public function getHttpClient(): FiscalApiHttpClientInterface;
}