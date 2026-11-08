<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Datos de un WebhookEvent: el recurso que cambió y sus estados. Inmutable.
 */
final class WebhookEventData
{
    private ?WebhookRelatedObject $relatedObject;

    private ?string $status;

    private ?string $previousStatus;

    private ?string $reason;

    public function __construct(
        ?WebhookRelatedObject $relatedObject,
        ?string $status,
        ?string $previousStatus,
        ?string $reason = null
    ) {
        $this->relatedObject = $relatedObject;
        $this->status = $status;
        $this->previousStatus = $previousStatus;
        $this->reason = $reason;
    }

    /**
     * El recurso que cambió de estado.
     */
    public function getRelatedObject(): ?WebhookRelatedObject
    {
        return $this->relatedObject;
    }

    /**
     * Estado al que llegó el recurso, con los códigos públicos de la API v4 (factura 'V', 'C', 'X'; solicitud de descarga
     * '1', '2', '3', '-1'; regla de descarga '1'..'4', '-1'); null si el tipo de evento no lo lleva.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Estado del que salió el recurso, con los mismos códigos que getStatus(); null si no aplica.
     */
    public function getPreviousStatus(): ?string
    {
        return $this->previousStatus;
    }

    /**
     * Motivo de la transición. Todo evento lo trae; es null salvo en WebhookEventTypes::INVOICE_CANCELLATION_REJECTED,
     * donde dice por qué no procedió la cancelación ('rejectedByRecipient', 'expired', 'notRequested' o
     * 'notCancelable'). Es texto libre: pueden aparecer valores nuevos.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}
