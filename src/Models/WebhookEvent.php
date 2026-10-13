<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

use DateTimeImmutable;

/**
 * Un evento de webhook de FiscalAPI: el mismo sobre para todo tipo de evento. Lo devuelve Webhook::verify()
 * (Fiscalapi\Webhooks\Webhook) después de validar la firma. Es una notificación ligera: para el estado autoritativo del
 * recurso (el orden de llegada no está garantizado), consulte getData()->getRelatedObject() con el servicio de su tipo
 * (por ejemplo getInvoiceService()->get()). Inmutable.
 */
final class WebhookEvent
{
    private ?string $id;

    private ?string $type;

    private ?DateTimeImmutable $createdAt;

    private ?string $personId;

    private ?WebhookEventData $data;

    public function __construct(
        ?string $id,
        ?string $type,
        ?DateTimeImmutable $createdAt,
        ?string $personId,
        ?WebhookEventData $data
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->personId = $personId;
        $this->data = $data;
    }

    /**
     * Id del evento; es también el header webhook-id y no cambia entre reintentos: úselo para descartar entregas repetidas.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Tipo de evento (ver WebhookEventTypes). Es texto: un tipo que esta versión del SDK no conoce llega tal cual, sin
     * error, y se ignora.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Momento del cambio de estado, en UTC, al segundo.
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Persona del tenant dueña del recurso; null si el recurso no tiene una (factura emitida por valores).
     */
    public function getPersonId(): ?string
    {
        return $this->personId;
    }

    /**
     * El recurso que cambió y sus estados.
     */
    public function getData(): ?WebhookEventData
    {
        return $this->data;
    }
}
