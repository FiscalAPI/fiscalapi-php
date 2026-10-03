# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP SDK for FiscalAPI — a Mexican electronic invoicing (CFDI) platform. Package: `fiscalapi/fiscalapi`, licensed MPL-2.0, requires PHP >= 7.4.

## Build & Dependencies

```bash
composer install          # Install dependencies
composer dump-autoload    # Regenerate autoloader after namespace/class changes
```

No linting or static analysis tools are configured.

## Tests

```bash
composer test             # PHPUnit 9.6 (the last series that runs on PHP 7.4) over tests/
```

PHPUnit needs `ext-mbstring`. A PHP without a `php.ini` can load it per call: `php -d extension_dir=<php>/ext -d extension=mbstring vendor/bin/phpunit`.

`tests/` holds offline tests (`Fiscalapi\Tests\`, `autoload-dev`). `FakeFiscalApiHttpClient` implements `FiscalApiHttpClientInterface` and answers every request with a fixed JSON from `tests/fixtures/` (shaped like the API's camelCase responses, enums as integers), so each test runs the real service → `FiscalApiHttpResponse::getJson()` path. `ResponseToleranceTest` characterizes how the SDK handles responses of upcoming API phases.

## Architecture

**Two-layer design:** HTTP layer (`src/Http/`) and Service layer (`src/Services/`), plus `src/Models/` holding constant-only classes (no DTOs — the SDK is array-based).

### HTTP Layer (`Fiscalapi\Http`)
- `FiscalApiSettings` — Configuration object (API URL, key, tenant, version, debug, SSL, timezone)
- `FiscalApiHttpClient` — Guzzle wrapper that auto-injects `X-API-KEY`, `X-TENANT-KEY`, `X-TIME-ZONE` headers. Debug mode logs requests/responses and disables SSL.
- `FiscalApiHttpResponse` — PSR-7 response wrapper with JSON caching

### Service Layer (`Fiscalapi\Services`)
- `AbstractService` — Base CRUD: `list()`, `get()`, `create()`, `update()`, `delete()`. Most services extend this; `SatValidationService`, `EmployeeService` and `EmployerService` are standalone.
- `FiscalApiClient` — Main entry point. Lazy-loads 11 services via getters (e.g., `getInvoiceService()`). `EmployeeService` and `EmployerService` are reached through `getPersonService()`.
- `FiscalApiClientFactory` — Static factory with instance caching keyed by `apiKey:tenant:apiUrl`.

### Notable Service Implementations
- **InvoiceService** — Every invoice goes to a single `POST /invoices` carrying `typeCode` in the body (`'I'` income, `'E'` credit note, `'P'` payment, `'N'` payroll). Has specialized methods: `cancel()`, `getPdf()`, `getXml()`, `send()`, `getStatus()`.
- **CatalogService** — Custom `search(catalogName, searchText)` and `getById(catalogName, id)` methods. Min search term: 4 chars.
- **DownloadCatalogService** — Throws `BadMethodCallException` for standard CRUD; uses `getList()` and `listCatalog()` instead.
- **DownloadRequestService** — Methods for downloading XMLs, metadata, ZIP packages, and SAT request/response files.
- **SatValidationService** — SAT validations over a CFDI or an RFC. Standalone (not CRUD): `getTypes()`, `getTypeById()`, `getStatuses()`, `validate()`. Each requested validation type consumes one validation credit.
- **StampService** — Credit ledger. `transferStamps()` takes an optional `creditType` (`CreditType::STAMP` or `CreditType::VALIDATION`); `withdrawStamps()` is deprecated and delegates to it.

### Models (`Fiscalapi\Models`)
Constant-only classes, no DTOs: `SatValidationTypeIds`, `SatValidationStatusIds`, `CreditType`. Values mirror the API contract exactly; identifiers follow PHP's `UPPER_SNAKE_CASE`.

## Coding Conventions

- `declare(strict_types=1)` in all files
- PSR-4 autoloading: `Fiscalapi\` → `src/`
- Explicit return types and parameter type hints throughout
- Classes: PascalCase, Methods: camelCase, Constants: UPPER_SNAKE_CASE
- Interfaces live alongside implementations in the same directory
- Error handling: `InvalidArgumentException` (validation), `RuntimeException` (HTTP errors with chained exceptions), `BadMethodCallException` (unsupported operations)

## Configuration

Environment variables used in examples and Laravel integration:
```
FISCALAPI_URL, FISCALAPI_KEY, FISCALAPI_TENANT, FISCALAPI_DEBUG,
FISCALAPI_VERIFY_SSL, FISCALAPI_API_VERSION, FISCALAPI_TIMEZONE
```

Default timezone: `America/Mexico_City`. API version: `v4`.

## CI/CD

GitHub Actions workflow (`.github/workflows/CICD.yml`) triggers on tags to update Packagist via API token.
