<?php
declare(strict_types=1);

namespace Fiscalapi\Webhooks;

use RuntimeException;

/**
 * La entrega no se puede tratar como auténtica: falta un header de Standard Webhooks, ninguna firma coincide, el
 * timestamp está fuera de la tolerancia, el cuerpo no es UTF-8 válido o el cuerpo firmado no es un evento. El receptor la
 * rechaza (por ejemplo con un 400) y no la procesa. getPrevious() conserva la causa original cuando la hay.
 */
class WebhookVerificationException extends RuntimeException
{
}
