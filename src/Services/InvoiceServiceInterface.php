<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Interfaz para el servicio de facturas
 */
interface InvoiceServiceInterface extends FiscalApiServiceInterface
{
    /**
     * Crea una nueva factura, nota de crédito o complemento de pago
     * 
     * @param array $data Datos de la factura
     * @return FiscalApiHttpResponseInterface
     */
    public function create(array $data): FiscalApiHttpResponseInterface;

    /**
     * Cancela una factura
     * 
     * @param array $data Solicitud para cancelar factura
     * @return FiscalApiHttpResponseInterface
     */
    public function cancel(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el PDF de una factura
     * 
     * @param array $data Solicitud para crear PDF
     * @return FiscalApiHttpResponseInterface
     */
    public function getPdf(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el XML de una factura
     * 
     * @param string $id Id de la factura
     * @return FiscalApiHttpResponseInterface
     */
    public function getXml(string $id): FiscalApiHttpResponseInterface;

    /**
     * Envía una factura por correo electrónico
     * 
     * @param array $data Solicitud para enviar factura
     * @return FiscalApiHttpResponseInterface
     */
    public function send(array $data): FiscalApiHttpResponseInterface;

    /**
     * Obtiene el estado de una factura
     * 
     * @param array $data Solicitud para consultar estado
     * @return FiscalApiHttpResponseInterface
     */
    public function getStatus(array $data): FiscalApiHttpResponseInterface;
}