<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Interfaz para el servicio de timbres (stamps).
 *
 * El recurso es un ledger de créditos. Cada movimiento indica con 'creditType' qué saldo mueve:
 * timbres o créditos de validación. Los saldos nunca se mezclan.
 */
interface StampServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Obtiene una lista de transacciones de timbres
     *
     * Devuelve en 'data' un objeto paginado con las claves 'items', 'pageNumber', 'totalPages',
     * 'totalCount', 'hasPreviousPage' y 'hasNextPage'. Cada elemento de 'items' incluye
     * 'creditType' con el tipo de crédito que movió la transacción.
     *
     * @param int $pageNumber Número de página
     * @param int $pageSize Tamaño de página, entre 1 y 50
     * @return FiscalApiHttpResponseInterface
     */
    public function list(int $pageNumber = 1, int $pageSize = 10): FiscalApiHttpResponseInterface;

    /**
     * Transfiere créditos de una persona a otra.
     *
     * Claves del arreglo:
     * - 'fromPersonId' (string, obligatorio): id de la persona de origen.
     * - 'toPersonId' (string, obligatorio): id de la persona de destino.
     * - 'amount' (int, obligatorio): cantidad a transferir, mayor que cero.
     * - 'comments' (string, opcional): hasta 100 caracteres.
     * - 'creditType' (int, opcional): CreditType::STAMP para timbres o CreditType::VALIDATION
     *   para créditos de validación. Si se omite, se transfieren timbres.
     *
     * El saldo que se valida es el que corresponde al 'creditType' enviado.
     *
     * @param array $data Datos de la transferencia
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si falta un campo obligatorio o 'creditType' no es un valor válido.
     */
    public function transferStamps(array $data): FiscalApiHttpResponseInterface;

    /**
     * Retira créditos de una persona.
     *
     * Acepta las mismas claves que transferStamps() y realiza la misma operación: un retiro es
     * una transferencia con 'fromPersonId' y 'toPersonId' invertidos.
     *
     * @param array $data Datos del retiro
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si falta un campo obligatorio o 'creditType' no es un valor válido.
     * @deprecated Utiliza transferStamps() invirtiendo 'fromPersonId' y 'toPersonId'.
     */
    public function withdrawStamps(array $data): FiscalApiHttpResponseInterface;
}
