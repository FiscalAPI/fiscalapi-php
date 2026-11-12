<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Valores del filtro 'order' de getInvoiceService()->getList(): el orden de la lista por fecha de creación.
 */
final class InvoiceListOrder
{
    /** De la más antigua a la más reciente (el orden por omisión). */
    public const ASC = 'asc';

    /** Las más recientes primero. */
    public const DESC = 'desc';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
