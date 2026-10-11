<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;
use InvalidArgumentException;

/**
 * Implementación del servicio de tickets de Auto CFDI.
 */
class TicketService implements TicketServiceInterface
{
    private const IDEMPOTENCY_KEY_HEADER = 'Idempotency-Key';

    protected FiscalApiHttpClientInterface $httpClient;
    protected string $resourcePath = 'tickets';

    /**
     * Constructor del servicio de tickets
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $ticket, ?string $idempotencyKey = null): FiscalApiHttpResponseInterface
    {
        $options = ['data' => $ticket];
        if ($idempotencyKey !== null) {
            $options['headers'] = [self::IDEMPOTENCY_KEY_HEADER => $idempotencyKey];
        }

        return $this->httpClient->post($this->buildResourceUrl(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $filters = []): FiscalApiHttpResponseInterface
    {
        $query = $this->buildQueryString($filters);

        return $this->httpClient->get($this->buildResourceUrl() . ($query === '' ? '' : '?' . $query));
    }

    /**
     * {@inheritdoc}
     */
    public function getById(string $id, bool $details = false): FiscalApiHttpResponseInterface
    {
        $this->validateId($id);

        // Como get() de los demás servicios: details viaja siempre, como 'true' o 'false'.
        return $this->httpClient->get(
            $this->buildResourceUrl($id),
            ['query_params' => ['details' => $details ? 'true' : 'false']]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(string $id): FiscalApiHttpResponseInterface
    {
        $this->validateId($id);

        return $this->httpClient->get($this->buildResourceUrl($id, 'code'));
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(string $id): FiscalApiHttpResponseInterface
    {
        $this->validateId($id);

        return $this->httpClient->delete($this->buildResourceUrl($id));
    }

    /**
     * Construye la URL del recurso.
     *
     * @param string|null $id Id del ticket (opcional)
     * @param string|null $subPath Subruta adicional (opcional)
     * @return string
     */
    protected function buildResourceUrl(?string $id = null, ?string $subPath = null): string
    {
        $url = '/' . $this->resourcePath;

        if ($id !== null) {
            $url .= '/' . rawurlencode($id);
        }

        if ($subPath !== null) {
            $url .= '/' . $subPath;
        }

        return $url;
    }

    /**
     * La query string de los filtros asignados, sin el signo '?'. Un arreglo repite la llave por cada valor
     * (status=pending&status=invoiced), los null se omiten y los booleanos viajan como 'true' o 'false'. Se arma aquí
     * porque la opción query de Guzzle numera los arreglos (status[0]=…), que el API no lee.
     *
     * @param array $filters Filtros de la lista
     * @return string
     */
    private function buildQueryString(array $filters): string
    {
        $pairs = [];
        foreach ($filters as $key => $value) {
            // (array) null es []: un filtro en null no se envía.
            foreach ((array) $value as $item) {
                $text = is_bool($item) ? ($item ? 'true' : 'false') : (string) $item;
                $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode($text);
            }
        }

        return implode('&', $pairs);
    }

    /**
     * Sin id, la ruta sería la de la lista (GET) o la colección (DELETE): se rechaza antes de llamar al API.
     *
     * @param string $id Id del ticket
     * @throws InvalidArgumentException
     */
    private function validateId(string $id): void
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException('Se requiere el id del ticket');
        }
    }
}
