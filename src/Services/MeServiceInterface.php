<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Servicio de la persona de la credencial con que se llama al API.
 */
interface MeServiceInterface
{
    /**
     * Obtiene la persona de la API key. GET /api/v4/me
     *
     * En 'data' llegan 'id', 'legalName', 'tin', 'email', 'tenantId', 'environment' (Development, Test o Production),
     * 'isOwner', 'features' (roles, permisos concedidos y negados, features del tenant, suscripción y asientos) y
     * 'balances' (saldo por tipo de crédito; un tipo ausente tiene saldo 0). Sirve para validar una credencial: con una
     * API key o un tenant que no son válidos responde 401.
     *
     * @return FiscalApiHttpResponseInterface
     */
    public function get(): FiscalApiHttpResponseInterface;
}
