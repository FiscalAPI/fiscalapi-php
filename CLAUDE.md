# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP SDK for FiscalAPI — a Mexican electronic invoicing (CFDI) platform. Package: `fiscalapi/fiscalapi`, licensed MPL-2.0, requires PHP >= 7.4.

## Build & Dependencies

```bash
composer install          # Install dependencies
composer dump-autoload    # Regenerate autoloader after namespace/class changes
```

No test suite exists in the repo. No linting or static analysis tools are configured.

## Architecture

**Two-layer design:** HTTP layer (`src/Http/`) and Service layer (`src/Services/`).

### HTTP Layer (`Fiscalapi\Http`)
- `FiscalApiSettings` — Configuration object (API URL, key, tenant, version, debug, SSL, timezone)
- `FiscalApiHttpClient` — Guzzle wrapper that auto-injects `X-API-KEY`, `X-TENANT-KEY`, `X-TIME-ZONE` headers. Debug mode logs requests/responses and disables SSL.
- `FiscalApiHttpResponse` — PSR-7 response wrapper with JSON caching

### Service Layer (`Fiscalapi\Services`)
- `AbstractService` — Base CRUD: `list()`, `get()`, `create()`, `update()`, `delete()`. All services extend this.
- `FiscalApiClient` — Main entry point. Lazy-loads 9 services via getters (e.g., `getInvoiceService()`).
- `FiscalApiClientFactory` — Static factory with instance caching keyed by `apiKey:tenant:apiUrl`.

### Notable Service Implementations
- **InvoiceService** — Type-based routing: `'I'` → `income`, `'E'` → `credit-note`, `'P'` → `payment`. Has specialized methods: `cancel()`, `getPdf()`, `getXml()`, `send()`, `getStatus()`.
- **CatalogService** — Custom `search(catalogName, searchText)` and `getById(catalogName, id)` methods. Min search term: 4 chars.
- **DownloadCatalogService** — Throws `BadMethodCallException` for standard CRUD; uses `getList()` and `listCatalog()` instead.
- **DownloadRequestService** — Methods for downloading XMLs, metadata, ZIP packages, and SAT request/response files.

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
