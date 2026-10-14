<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * Tipos de evento de webhook, como llegan en WebhookEvent::getType(). Los de un cambio de estado nombran el estado al que
 * llegó el recurso. Son una ayuda para comparar: el catálogo puede crecer y un tipo desconocido llega tal cual, como texto,
 * y se ignora.
 */
final class WebhookEventTypes
{
    /** La factura pasó de vigente a cancelación en proceso ('V' → 'C'). */
    public const INVOICE_CANCELLATION_REQUESTED = 'invoice.cancellation_requested';

    /** La cancelación en proceso terminó y la factura quedó cancelada ('C' → 'X'). */
    public const INVOICE_CANCELED = 'invoice.canceled';

    /** La cancelación en proceso se rechazó y la factura volvió a vigente ('C' → 'V'). */
    public const INVOICE_CANCELLATION_REJECTED = 'invoice.cancellation_rejected';

    /** La solicitud de descarga masiva terminó con éxito (→ '3'). */
    public const DOWNLOAD_REQUEST_COMPLETED = 'download_request.completed';

    /**
     * La solicitud de descarga masiva se abandonó (→ '-1'). No implica un error: el motivo está en la solicitud
     * (getDownloadRequestService()->get()).
     */
    public const DOWNLOAD_REQUEST_ABANDONED = 'download_request.abandoned';

    /** La regla de descarga masiva quedó suspendida ('2' → '4'). */
    public const DOWNLOAD_RULE_SUSPENDED = 'download_rule.suspended';

    /**
     * Prueba de un webhook: no se suscribe; llega solo al pulsar "Enviar prueba" (POST /api/v4/webhooks/{id}/test), y
     * solo a ese webhook. Su relatedObject es el webhook mismo (type 'webhook', url '/api/v4/webhooks/{id}'), sin persona
     * ni estados.
     */
    public const WEBHOOK_TEST = 'webhook.test';

    /**
     * Contenedor de constantes: no se instancia.
     */
    private function __construct()
    {
    }
}
