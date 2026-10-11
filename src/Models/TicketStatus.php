<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Estados de un ticket de Auto CFDI, como llegan en la clave 'status' (texto camelCase).
 *
 * El SDK entrega la respuesta como arreglo: un estado que esta versión no conoce llega tal cual, como texto.
 */
final class TicketStatus
{
    /** Ingerido y sin factura: lo puede facturar el receptor o entrar a una global. */
    public const PENDING = 'pending';

    /** El receptor lo está facturando. */
    public const INVOICING = 'invoicing';

    /** Tiene su factura individual ('invoiceId'). */
    public const INVOICED = 'invoiced';

    /** Una factura global lo está timbrando. */
    public const GLOBALIZING = 'globalizing';

    /** Está en una factura global ('globalInvoiceId'). */
    public const GLOBALIZED = 'globalized';

    /** Cancelado por el tenant antes de facturarse. */
    public const CANCELLED = 'cancelled';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
