<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;

class ProductService extends AbstractService implements ProductServiceInterface
{
    /**
     * Constructor del servicio de productos
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'products');
    }

}
