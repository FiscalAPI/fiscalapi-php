<?php
declare(strict_types=1);

namespace Fiscalapi\Services;

use Fiscalapi\Http\FiscalApiHttpClientInterface;
use Fiscalapi\Http\FiscalApiHttpResponseInterface;

/**
 * Implementación del servicio de personas
 */
class PersonService extends AbstractService implements PersonServiceInterface
{
    private ?EmployeeServiceInterface $employeeService = null;
    private ?EmployerServiceInterface $employerService = null;

    /**
     * Constructor del servicio de personas
     *
     * @param FiscalApiHttpClientInterface $httpClient Cliente HTTP
     */
    public function __construct(FiscalApiHttpClientInterface $httpClient)
    {
        parent::__construct($httpClient, 'people');
        $this->employeeService = new EmployeeService($httpClient);
        $this->employerService = new EmployerService($httpClient);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmployeeService(): EmployeeServiceInterface
    {
        return $this->employeeService;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmployerService(): EmployerServiceInterface
    {
        return $this->employerService;
    }
}