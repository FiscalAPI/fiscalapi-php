<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

/**
 * Query string de los filtros de una lista (tickets, facturas, personas y productos).
 *
 * @internal
 */
final class ListQuery
{
    /**
     * La query string de los filtros asignados, sin el signo '?'. Un arreglo repite la llave por cada valor
     * (status=pending&status=invoiced), los null y los false se omiten y true viaja como 'true'. Se arma aquí porque la
     * opción query de Guzzle numera los arreglos (status[0]=…), que el API no lee.
     *
     * @param array $filters Filtros de la lista
     * @return string
     */
    public static function build(array $filters): string
    {
        $pairs = [];
        foreach ($filters as $key => $value) {
            // (array) null es []: un filtro en null no se envía. Un false tampoco: details solo viaja como true.
            foreach ((array) $value as $item) {
                if ($item === false) {
                    continue;
                }
                $text = $item === true ? 'true' : (string) $item;
                $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode($text);
            }
        }

        return implode('&', $pairs);
    }

    private function __construct()
    {
    }
}
