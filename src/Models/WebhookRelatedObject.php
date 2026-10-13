<?php
declare(strict_types=1);

namespace Fiscalapi\Models;

/**
 * El recurso al que se refiere un WebhookEvent. Inmutable.
 */
final class WebhookRelatedObject
{
    private ?string $id;

    private ?string $type;

    private ?string $url;

    public function __construct(?string $id, ?string $type, ?string $url)
    {
        $this->id = $id;
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * Id del recurso: el que recibe el get() del servicio de su tipo (por ejemplo getInvoiceService()->get()).
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Tipo de recurso: 'invoice', 'download_request', 'download_rule' o 'webhook_endpoint' (el de
     * WebhookEventTypes::WEBHOOK_ENDPOINT_TEST); el conjunto puede crecer.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Ruta relativa del GET v4 del recurso (por ejemplo '/api/v4/invoices/{id}'), sin host: el mismo evento es idéntico en
     * pruebas y en producción. Para leer el recurso use el servicio de su tipo con getId().
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
