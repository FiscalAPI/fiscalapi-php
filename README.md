# FiscalAPI SDK para PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/fiscalapi/fiscalapi.svg)](https://packagist.org/packages/fiscalapi/fiscalapi)
[![License](https://img.shields.io/github/license/FiscalAPI/fiscalapi-php)](https://github.com/FiscalAPI/fiscalapi-php/blob/master/LICENSE.txt) 

**SDK oficial de FiscalAPI para PHP**, la API de facturación CFDI y otros servicios fiscales en México. Simplifica la integración con los servicios de facturación electrónica, eliminando las complejidades del SAT y facilitando la generación de facturas, notas de crédito, complementos de pago, nómina, carta porte, y más. ¡Factura sin dolor!


## 📋 Facturación CFDI 4.0
- **Soporte completo para CFDI 4.0** con todas las especificaciones oficiales
- **Timbrado de facturas de ingreso** con validación automática
- **Timbrado de notas de crédito** (facturas de egreso)
- **Timbrado de complementos de pago** en MXN, USD y EUR.
- **Consulta del estatus de facturas** en el SAT en tiempo real
- **Cancelación de facturas**
- **Generación de archivos PDF** de las facturas con formato profesional
- **Personalización de logos y colores** en los PDF generados
- **Envío de facturas por correo electrónico** automatizado
- **Descarga de archivos XML** con estructura completa
- **Almacenamiento y recuperación** de facturas por 5 años.
- Dos [modos de operación](https://docs.fiscalapi.com/modes-of-operation): **Por valores** o **Por referencias**

## 📥 Descarga Masiva
- **Acceso a catálogos de descarga masiva** del SAT
- **Descarga de CFDI y Metadatos** en lotes grandes
- **Descarga masiva XML** con filtros personalizados
- **Reglas de descarga automática por RFC**
- **Solicitudes de descarga** via API y Dashboard.
- **Automatización de solicitudes de descarga**

## 👥 Gestión de Personas
- **Administración de personas** (emisores, receptores, clientes, usuarios, etc.)
- **Gestión de certificados CSD y FIEL** (subir archivos .cer y .key a FiscalAPI)
- **Configuración de datos fiscales** (RFC, domicilio fiscal, régimen fiscal)
- **Gestión de datos de empleador** (patrón) asociados a una persona
- **Gestión de datos de empleado** (trabajador) asociados a una persona

## 🛍️ Gestión de Productos/Servicios
- **Gestión de productos y servicios** con catálogo personalizable
- **Administración de impuestos aplicables** (IVA, ISR, IEPS)

## 📚 Consulta de Catálogos SAT
- **Consulta en catálogos oficiales del SAT** actualizados
- **Consulta en catálogos oficiales de Descarga masiva del SAT** actualizados
- **Búsqueda de información** en catálogos del SAT con filtros avanzados
- **Acceso y búsqueda** en catálogos completos

## 📖 Recursos Adicionales
- **Cientos de ejemplos de código** disponibles en múltiples lenguajes de programación
- Documentación completa con guías paso a paso
- Ejemplos prácticos para casos de uso comunes
- Soporte técnico especializado
- Actualizaciones regulares conforme a cambios del SAT

## 📦 Instalación
Compatible con **PHP 7.4** y superiores

**Composer**:

```bash
composer require fiscalapi/fiscalapi
```

## ⚙️ Configuración

El SDK se puede utilizar tanto en aplicaciones básicas de PHP como en frameworks que utilizan inyección de dependencias (como Laravel o Symfony).

### 1. Configuración de Variables de Entorno

Crea o actualiza tu archivo `.env` con las siguientes variables:

```
# FiscalAPI Configuration
FISCALAPI_URL=https://test.fiscalapi.com # https://live.fiscalapi.com (produción)
FISCALAPI_KEY=tu_api_key
FISCALAPI_TENANT=tu_tenant_id
FISCALAPI_DEBUG=false
FISCALAPI_VERIFY_SSL=true
FISCALAPI_API_VERSION=v4
FISCALAPI_TIMEZONE=America/Mexico_City
```

### Configuración con inyección de dependencias

#### Laravel

##### Paso 1: Crear el archivo de configuración

Crea el archivo `config/fiscalapi.php`:

```php
<?php

return [
    'apiUrl' => env('FISCALAPI_URL', 'https://test.fiscalapi.com'),
    'apiKey' => env('FISCALAPI_KEY', ''),
    'tenant' => env('FISCALAPI_TENANT', ''),
    'debug' => env('FISCALAPI_DEBUG', false),
    'verifySsl' => env('FISCALAPI_VERIFY_SSL', true),
    'apiVersion' => env('FISCALAPI_API_VERSION', 'v4'),
    'timeZone' => env('FISCALAPI_TIMEZONE', 'America/Mexico_City')
];
```

##### Paso 2: Generar un Service Provider con Artisan

Utiliza el siguiente comando Artisan para generar el Service Provider:

```bash
php artisan make:provider FiscalApiServiceProvider
```

##### Paso 3: Modificar el Service Provider generado

Abre el archivo creado en `app/Providers/FiscalApiServiceProvider.php` y modifícalo de la siguiente manera:

```php
<?php

namespace App\Providers;

use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Support\ServiceProvider;

class FiscalApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/fiscalapi.php', 'fiscalapi'
        );

        $this->app->singleton(FiscalApiClient::class, function ($app) {
            $config = config('fiscalapi');

            $settings = new FiscalApiSettings(
                $config['apiUrl'],
                $config['apiKey'],
                $config['tenant'],
                $config['debug'] ?? false,
                $config['verifySsl'] ?? true,
                $config['apiVersion'] ?? 'v4',
                $config['timeZone'] ?? 'America/Mexico_City'
            );

            return new FiscalApiClient($settings);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/fiscalapi.php' => config_path('fiscalapi.php'),
        ], 'fiscalapi-config');
    }
}

```

##### Paso 4: Registrar el Service Provider

Dependiendo de tu versión de Laravel, registra el provider en la ubicación adecuada:

**Laravel 8 y anteriores** - En `config/app.php`:
```php
'providers' => [
    // Otros providers...
    App\Providers\FiscalApiServiceProvider::class,
],
```

**Laravel 9+** - En `bootstrap/providers.php`:
```php
return [
    // Otros providers...
    App\Providers\AppServiceProvider::class,
    App\Providers\FiscalApiServiceProvider::class,
];
```


##### Usar el cliente mediante inyección de dependencias donde lo necesites

```php
<?php
namespace App\Http\Controllers;

use Fiscalapi\Services\FiscalApiClient;

class FacturasController extends Controller
{
    private $fiscalApi;

    public function __construct(FiscalApiClient $fiscalApi)
    {
        $this->fiscalApi = $fiscalApi;
    }
    
    public function createInvoice()
    {
        // Usar $this->fiscalApi para generar facturas
    }
}
```

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
$fiscalApi = new \Fiscalapi\Services\FiscalApiClient($settings);

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
  Crear facturas de ingreso, notas de crédito, complementos de pago, nómina, cancelaciones, generación de PDF/XML.
- **Nómina**  
  Crear facturas de nómina (typeCode 'N') con complemento de nómina (percepciones, deducciones, etc.).
- **Impuestos Locales**  
  Agregar complemento de impuestos locales (traslados y retenciones locales) a facturas de ingreso.
- **Empleadores y Empleados**  
  Gestión de datos de empleador (patrón) y empleado asociados a personas, necesarios para factuación de nómina.
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
- [Ejemplos PHP](https://github.com/FiscalAPI/fiscalapi-php/blob/main/examples.php)  
- [Ejemplos Laravel](https://github.com/FiscalAPI/fiscalapi-samples-laravel)


---

Desarrollado con ❤️ por [Fiscalapi](https://www.fiscalapi.com)
