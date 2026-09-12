<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Tipo de crédito que mueve una transacción del ledger de timbres.
 *
 * Los saldos nunca se mezclan: STAMP afecta 'availableBalance' y VALIDATION afecta
 * 'availableValidationBalance'. Se envía y se recibe como entero.
 */
final class CreditType
{
    /** Timbres. Es el valor que asume la API cuando la clave 'creditType' se omite. */
    public const STAMP = 1;

    /** Créditos de validación, los que consumen las validaciones SAT. */
    public const VALIDATION = 2;

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
