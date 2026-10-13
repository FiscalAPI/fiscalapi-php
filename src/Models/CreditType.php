<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Tipo de crédito que mueve una transacción del ledger de timbres.
 *
 * Los saldos nunca se mezclan: STAMP afecta 'availableBalance', VALIDATION afecta
 * 'availableValidationBalance' y TICKET afecta el saldo de tipo TICKET en 'balances' de la persona.
 * Se envía y se recibe como entero; un tipo que este SDK no conoce llega como su número.
 */
final class CreditType
{
    /** Timbres. Es el valor que asume la API cuando la clave 'creditType' se omite. */
    public const STAMP = 1;

    /** Créditos de validación, los que consumen las validaciones SAT. */
    public const VALIDATION = 2;

    /** Créditos de ticket. Su saldo se lee en 'balances' de la persona. */
    public const TICKET = 3;

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
