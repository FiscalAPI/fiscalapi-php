# FiscalAPI SDK para PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/fiscalapi/fiscalapi.svg)](https://packagist.org/packages/fiscalapi/fiscalapi)
[![License](https://img.shields.io/github/license/FiscalAPI/fiscalapi-php)](https://github.com/FiscalAPI/fiscalapi-php/blob/master/LICENSE.txt) 

**SDK oficial de FiscalAPI para PHP**, la API de facturación CFDI y otros servicios fiscales en México. Simplifica la integración con los servicios de facturación electrónica, eliminando las complejidades del SAT y facilitando la generación de facturas, notas de crédito, complementos de pago, nómina, carta porte, y más. ¡Factura sin dolor!

## 🚀 Características

- Soporte completo para **CFDI 4.0**  
- Compatible con **PHP 7.4** y superiores
- Operaciones asíncronas y sincrónicas
- Dos modos de operación: **Por valores** o **Por referencias**
- Manejo simplificado de errores
- Búsqueda en catálogos del SAT
- Documentación completa y ejemplos prácticos

## 📦 Instalación

**Composer**:

```bash
composer require fiscalapi/fiscalapi
```

## ⚙️ Configuración

Puedes usar el SDK tanto en aplicaciones sin inyección de dependencias como en proyectos que usan DI (Laravel, Symfony, etc.). A continuación se describen ambas formas:

### A) Aplicaciones sin Inyección de Dependencias

1. **Crea tu objeto de configuración** con [tus credenciales](https://docs.fiscalapi.com/credentials-info):
    ```php
    $settings = [
        'apiUrl' => 'https://test.fiscalapi.com', // https://live.fiscalapi.com (producción)
        'apiKey' => '<tu_api_key>',
        'tenant' => '<tenant>'
    ];
    ```

2. **Crea la instancia del cliente**:
    ```php
    $fiscalApi = new \Fiscalapi\FiscalApiClient($settings);
    ```

Para ejemplos completos, consulta [vanilla-php-examples](https://github.com/FiscalAPI/fiscalapi-samples-php-vanilla).

---

### B) Aplicaciones con Inyección de Dependencias (Laravel, Symfony, etc.)

1. **Agrega la sección de configuración** en tu archivo de configuración:

    **Laravel** (`config/fiscalapi.php`):
    ```php
    <?php
    return [
        'apiUrl' => env('FISCALAPI_URL', 'https://test.fiscalapi.com'),
        'apiKey' => env('FISCALAPI_KEY', ''),
        'tenant' => env('FISCALAPI_TENANT', '')
    ];
    ```

2. **Registra los servicios** en el contenedor (por ejemplo, en un Service Provider):

    **Laravel**:
    ```php
    <?php
    
    namespace App\Providers;
    
    use Fiscalapi\FiscalApiClient;
    use Illuminate\Support\ServiceProvider;
    
    class FiscalApiServiceProvider extends ServiceProvider
    {
        public function register()
        {
            $this->app->singleton(FiscalApiClient::class, function ($app) {
                return new FiscalApiClient(config('fiscalapi'));
            });
        }
    }
    ```

3. **Inyecta** `FiscalApiClient` donde lo requieras:

    ```php
    <?php
    
    namespace App\Http\Controllers;
    
    use Fiscalapi\FiscalApiClient;
    
    class InvoicesController extends Controller
    {
        private $fiscalApi;
    
        public function __construct(FiscalApiClient $fiscalApi)
        {
            $this->fiscalApi = $fiscalApi;
        }
        
        // Usa $this->fiscalApi en tus métodos de controlador...
    }
    ```

Para más ejemplos, revisa [samples-laravel](https://github.com/FiscalAPI/fiscalapi-samples-php-laravel).


## 🔄 Modos de Operación

FiscalAPI admite dos [modos de operación](https://docs.fiscalapi.com/modes-of-operation):

- **Por Referencias**: Envía solo IDs de objetos previamente creados en el dashboard de FiscalAPI.  
  Ideal para integraciones ligeras.

- **Por Valores**: Envía todos los campos requeridos en cada petición, con mayor control sobre los datos.  
  No se requiere configuración previa en el dashboard.


## 📝 Ejemplos de Uso

A continuación se muestran algunos ejemplos básicos para ilustrar cómo utilizar el SDK. Puedes encontrar más ejemplos en la [documentación oficial](https://docs.fiscalapi.com).

### 1. Crear una Persona (Emisor o Receptor)

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

$request = [
    'legalName' => 'Persona de Prueba',
    'email' => 'someone@somewhere.com',
    'password' => 'YourStrongPassword123!',
];

$apiResponse = $fiscalApi->persons->create($request);
```

### 2. Subir Certificados CSD
[Descarga certificados de prueba](https://docs.fiscalapi.com/tax-files-info)

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

$certificadoCsd = [
    'personId' => '984708c4-fcc0-43bd-9d30-ec017815c20e',
    'base64File' => 'MIIFsDCCA5igAwIBAgI...==', // Certificado .cer codificado en Base64
    'fileType' => 'CertificateCsd',
    'password' => '12345678a',
    'tin' => 'EKU9003173C9'
];

$clavePrivadaCsd = [
    'personId' => '984708c4-fcc0-43bd-9d30-ec017815c20e',
    'base64File' => 'MIIFDjBABgkqhkiG9w0BBQ0...==', // Llave privada .key codificada en Base64
    'fileType' => 'PrivateKeyCsd',
    'password' => '12345678a',
    'tin' => 'EKU9003173C9'
];

$apiResponseCer = $fiscalApi->taxFiles->create($certificadoCsd);
$apiResponseKey = $fiscalApi->taxFiles->create($clavePrivadaCsd);
```

### 3. Crear un Producto o Servicio

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

$request = [
    'description' => 'Servicios contables',
    'unitPrice' => 100,
    'satUnitMeasurementId' => 'E48',
    'satTaxObjectId' => '02',
    'satProductCodeId' => '84111500'
];

$apiResponse = $fiscalApi->products->create($request);
```

### 4. Actualizar Impuestos de un Producto

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

$request = [
    'id' => '310301b3-1ae9-441b-b463-51a8f9ca8ba2',
    'description' => 'Servicios contables',
    'unitPrice' => 100, 
    'satUnitMeasurementId' => 'E48',
    'satTaxObjectId' => '02',
    'satProductCodeId' => '84111500',
    'productTaxes' => [
        ['rate' => 0.16, 'taxId' => '002', 'taxFlagId' => 'T', 'taxTypeId' => 'Tasa'],  // IVA 16%
        ['rate' => 0.10, 'taxId' => '001', 'taxFlagId' => 'R', 'taxTypeId' => 'Tasa'],  // ISR 10%
        ['rate' => 0.10666666666, 'taxId' => '002', 'taxFlagId' => 'R', 'taxTypeId' => 'Tasa'] // IVA 2/3 partes
    ]
];

$apiResponse = $fiscalApi->products->update($request['id'], $request);
```

### 5. Crear una Factura de Ingreso (Por Referencias)

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

$invoice = [
    'versionCode' => '4.0',
    'series' => 'SDK-F',
    'date' => date('Y-m-d\TH:i:s'),
    'paymentFormCode' => '01',
    'currencyCode' => 'MXN',
    'typeCode' => 'I',
    'expeditionZipCode' => '42501',
    'issuer' => [
        'id' => '<id-emisor-en-fiscalapi>'
    ],
    'recipient' => [
        'id' => '<id-receptor-en-fiscalapi>'
    ],
    'items' => [
        [
            'id' => '<id-producto-en-fiscalapi>',
            'quantity' => 1,
            'discount' => 10.85
        ]
    ],
    'paymentMethodCode' => 'PUE',
];

$apiResponse = $fiscalApi->invoices->create($invoice);
```

### 6. Crear la Misma Factura de Ingreso (Por Valores)

```php
$fiscalApi = new \Fiscalapi\FiscalApiClient($settings);

// Agregar sellos CSD, Emisor, Receptor, Items, etc.
$invoice = [
    'versionCode' => '4.0',
    'series' => 'SDK-F',
    'date' => date('Y-m-d\TH:i:s'),
    'paymentFormCode' => '01',
    'currencyCode' => 'MXN',
    'typeCode' => 'I',
    'expeditionZipCode' => '42501',
    'issuer' => [
        'tin' => 'EKU9003173C9',
        'legalName' => 'ESCUELA KEMPER URGATE',
        'taxRegimeCode' => '601',
        'taxCredentials' => [
            [
                'base64File' => 'certificate_base64...',
                'fileType' => 'CertificateCsd',
                'password' => '12345678a'
            ],
            [
                'base64File' => 'private_key_base64...',
                'fileType' => 'PrivateKeyCsd',
                'password' => '12345678a'
            ]
        ]
    ],
    'recipient' => [
        'tin' => 'EKU9003173C9',
        'legalName' => 'ESCUELA KEMPER URGATE',
        'zipCode' => '42501',
        'taxRegimeCode' => '601',
        'cfdiUseCode' => 'G01',
        'email' => 'someone@somewhere.com'
    ],
    'items' => [
        [
            'itemCode' => '01010101',
            'quantity' => 9.5,
            'unitOfMeasurementCode' => 'E48',
            'description' => 'Invoicing software as a service',
            'unitPrice' => 3587.75,
            'taxObjectCode' => '02',
            'discount' => 255.85,
            'itemTaxes' => [
                [
                    'taxCode' => '002', // IVA
                    'taxTypeCode' => 'Tasa',
                    'taxRate' => 0.16,
                    'taxFlagCode' => 'T'
                ]
            ]
        ]
    ],
    'paymentMethodCode' => 'PUE',
];

$apiResponse = $fiscalApi->invoices->create($invoice);
```

### 7. Búsqueda en Catálogos del SAT

```php
// Busca los registros que contengan 'inter' en el catalogo 'SatUnitMeasurements' (pagina 1, tamaño pagina 10)
$apiResponse = $fiscalApi->catalogs->searchCatalog('SatUnitMeasurements', 'inter', 1, 10);

if ($apiResponse->succeeded) {
    foreach ($apiResponse->data->items as $item) {
        echo "Unidad: {$item->description}\n";
    }
} else {
    echo $apiResponse->message;
}
```

---

## 📋 Operaciones Principales

- **Facturas (CFDI)**  
  Crear facturas de ingreso, notas de crédito, complementos de pago, cancelaciones, generación de PDF/XML.
- **Personas (Clientes/Emisores)**  
  Alta y administración de personas, gestión de certificados (CSD).
- **Productos y Servicios**  
  Administración de catálogos de productos, búsqueda en catálogos SAT.


## 🤝 Contribuir

1. Haz un fork del repositorio.  
2. Crea una rama para tu feature: `git checkout -b feature/AmazingFeature`.  
3. Realiza commits de tus cambios: `git commit -m 'Add some AmazingFeature'`.  
4. Sube tu rama: `git push origin feature/AmazingFeature`.  
5. Abre un Pull Request en GitHub.


## 🐛 Reportar Problemas

1. Asegúrate de usar la última versión del SDK.  
2. Verifica si el problema ya fue reportado.  
3. Proporciona un ejemplo mínimo reproducible.  
4. Incluye los mensajes de error completos.


## 📄 Licencia

Este proyecto está licenciado bajo la Licencia **MPL**. Consulta el archivo [LICENSE](LICENSE.txt) para más detalles.


## 🔗 Enlaces Útiles

- [Documentación Oficial](https://docs.fiscalapi.com)  
- [Portal de FiscalAPI](https://fiscalapi.com)  
- [Ejemplos PHP](https://github.com/FiscalAPI/fiscalapi-samples-php-vanilla)  
- [Ejemplos Laravel](https://github.com/FiscalAPI/fiscalapi-samples-php-laravel)


---

Desarrollado con ❤️ por [Fiscalapi](https://www.fiscalapi.com)
