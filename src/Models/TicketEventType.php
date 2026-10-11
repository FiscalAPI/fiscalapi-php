<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Tipos de hecho de la historia de un ticket, como llegan en 'events[].type' (texto camelCase).
 *
 * Un tipo que esta versión del SDK no conoce llega tal cual, como texto.
 */
final class TicketEventType
{
    public const INGESTED = 'ingested';
    public const INVOICING_STARTED = 'invoicingStarted';
    public const INVOICED = 'invoiced';
    public const INVOICE_REJECTED = 'invoiceRejected';
    public const OUTCOME_UNKNOWN = 'outcomeUnknown';
    public const LEASE_EXPIRED = 'leaseExpired';
    public const GLOBALIZING = 'globalizing';
    public const GLOBALIZED = 'globalized';
    public const RELEASED_FROM_GLOBAL = 'releasedFromGlobal';
    public const CANCELLED = 'cancelled';
    public const RECOVERY_FAILED = 'recoveryFailed';
    public const RECOVERED = 'recovered';
    public const RESENT = 'resent';
    public const RETURNED_TO_PENDING = 'returnedToPending';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
