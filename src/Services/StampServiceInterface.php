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
     * - 'creditType' (int, opcional): CreditType::STAMP para timbres, CreditType::VALIDATION
     *   para créditos de validación o CreditType::TICKET para créditos de ticket. Si se omite, se transfieren timbres.
     *
     * El saldo que se valida es el que corresponde al 'creditType' enviado.
     *
     * Con $idempotencyKey (de 1 a 64 caracteres ASCII visibles, sin espacios) se envía el header Idempotency-Key.
     * Repetir la misma transferencia con la misma llave de idempotencia dentro de 24 h devuelve la respuesta original con el header
     * Idempotent-Replayed: true; otra petición con la misma llave de idempotencia responde 422, y una original
     * que aún se procesa, 409. Sin ella, el header no se envía.
     *
     * @param array $data Datos de la transferencia
     * @param string|null $idempotencyKey Valor del header Idempotency-Key, o null para no enviarlo
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si falta un campo obligatorio o 'creditType' no es un valor válido.
     */
    public function transferStamps(array $data, ?string $idempotencyKey = null): FiscalApiHttpResponseInterface;

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
