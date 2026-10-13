<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Quién actuó en un hecho del ticket, como llega en 'events[].actorType' (texto camelCase).
 *
 * Un actor que esta versión del SDK no conoce llega tal cual, como texto.
 */
final class TicketActorType
{
    /** Una persona del tenant por la API o el dashboard. */
    public const TENANT = 'tenant';

    /** El receptor en el portal de autofactura. */
    public const PORTAL = 'portal';

    /** Un proceso en segundo plano de FiscalAPI. */
    public const SYSTEM = 'system';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
