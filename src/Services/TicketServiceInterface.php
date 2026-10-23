<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Servicio de tickets de Auto CFDI: ingesta de ventas pagadas del punto de venta, consulta con filtros, código para
 * reimprimir y cancelación. Los tickets se envían y se reciben como arreglos con las claves camelCase del API.
 */
interface TicketServiceInterface
{
    /**
     * Ingiere un ticket y cobra 1 crédito de ticket. POST /api/v4/tickets
     *
     * Con $idempotencyKey (de 1 a 64 caracteres ASCII visibles, sin espacios) se envía el header Idempotency-Key: repetir
     * la misma llave de idempotencia con el mismo cuerpo, dentro de 24 h, devuelve la respuesta original (201, con el
     * header Idempotent-Replayed: true) sin registrar otro ticket ni cobrar otra vez; con otro cuerpo, 422; con la
     * original aún en proceso, 409 con Retry-After. Sin ella, el header no se envía. Un folio y serie ya activos del
     * emisor responden 409 con 'data.existingTicketId'; un total que no cuadra, 422 con 'data.expectedTotal' y
     * 'data.computedTotal'.
     *
     * @param array $ticket Ticket: issuer.id, folio, series, paymentDate (hora civil de México, Y-m-d\TH:i:s),
     *                      paymentFormCode, currencyCode, exchangeRate, expeditionZipCode, total e items con itemTaxes
     * @param string|null $idempotencyKey Llave de idempotencia (idempotency key), valor del header Idempotency-Key, o null
     *                                    para no enviarlo
     * @return FiscalApiHttpResponseInterface 201 con id, ticketCode, qrUrl, status, selfInvoiceEligible, eligibleUntil
     *                                        e importes; la repetición idempotente devuelve esa misma respuesta
     */
    public function create(array $ticket, ?string $idempotencyKey = null): FiscalApiHttpResponseInterface;

    /**
     * Lista los tickets del tenant con los filtros asignados. GET /api/v4/tickets
     *
     * @param array $filters 'status' (uno o más; se envía repitiendo la llave), 'personId', 'details' (true envía
     *                       details=true: conceptos con impuestos, sin la historia; false no se envía),
     *                       'globalInvoiceId' (factura global que ampara los tickets), 'from' y 'to'
     *                       (fecha civil de México Y-m-d), 'pageNumber' y 'pageSize'. Los valores null no se envían.
     * @return FiscalApiHttpResponseInterface Página de tickets ordenados por fecha de pago
     */
    public function getList(array $filters = []): FiscalApiHttpResponseInterface;

    /**
     * Obtiene un ticket; con $details trae también sus conceptos ('items') e historia ('events').
     * GET /api/v4/tickets/{id}?details=true
     *
     * @param string $id Id del ticket
     * @param bool $details Incluir conceptos e historia
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si el id está vacío
     */
    public function getById(string $id, bool $details = false): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el código del ticket y la URL de su QR, para reimprimirlo. GET /api/v4/tickets/{id}/code
     *
     * @param string $id Id del ticket
     * @return FiscalApiHttpResponseInterface ticketCode y qrUrl
     * @throws InvalidArgumentException Si el id está vacío
     */
    public function getCode(string $id): FiscalApiHttpResponseInterface;

    /**
     * Cancela un ticket en estado pending (409 en cualquier otro estado); el crédito de ticket no se devuelve.
     * DELETE /api/v4/tickets/{id}
     *
     * @param string $id Id del ticket
     * @return FiscalApiHttpResponseInterface
     * @throws InvalidArgumentException Si el id está vacío
     */
    public function cancel(string $id): FiscalApiHttpResponseInterface;
}
