# WorkOS Laravel v6 Migration Guide

This guide covers the changes required to migrate from v5 of `workos/workos-php-laravel` to v6.

v6 is a thin adapter over the redesigned v5 of the underlying `workos/workos-php` SDK. The `WorkOSService` wrapper has been removed — the Laravel service provider now binds an instantiated `\WorkOS\WorkOS` client directly. For conceptual background on the underlying changes, see the upstream [v5 migration guide](https://github.com/workos/workos-php/blob/main/docs/V5_MIGRATION_GUIDE.md).

## Table of Contents

- [WorkOS Laravel v6 Migration Guide](#workos-laravel-v6-migration-guide)
  - [Table of Contents](#table-of-contents)
  - [Quick Start](#quick-start)
  - [Requirements](#requirements)
  - [`WorkOSService` has been removed](#workosservice-has-been-removed)
    - [Dependency injection](#dependency-injection)
    - [Container resolution](#container-resolution)
    - [Helper function](#helper-function)
  - [Service accessor renames](#service-accessor-renames)
  - [Configuration changes](#configuration-changes)
  - [Facade](#facade)
  - [New services available](#new-services-available)

---

## Quick Start

1. Upgrade PHP to 8.2+ and Laravel to 11+.
2. Upgrade the package:

   ```bash
   composer require workos/workos-php-laravel:^6
   ```

3. Replace any references to `WorkOS\Laravel\Services\WorkOSService` with `WorkOS\WorkOS`.
4. Rename any calls to renamed service accessors (see table below).
5. Re-run your tests.

---

## Requirements

|                     | v5           | v6           |
| ------------------- | ------------ | ------------ |
| PHP                 | `>= 8.1`     | `>= 8.2`     |
| Laravel             | 10 / 11 / 12 | 11 / 12 / 13 |
| `workos/workos-php` | `^4.29`      | `^5.0.1`     |

Laravel 10 is no longer supported. Laravel 13 is newly supported, and requires PHP 8.3+.

---

## `WorkOSService` has been removed

The `WorkOS\Laravel\Services\WorkOSService` wrapper class has been removed. The v5 upstream SDK already provides lazy service accessors on `\WorkOS\WorkOS` itself, so the wrapper is redundant. The Laravel service provider now binds `\WorkOS\WorkOS` directly to the `workos` container key.

### Dependency injection

Before:

```php
use WorkOS\Laravel\Services\WorkOSService;

class UserController
{
    public function __construct(private WorkOSService $workos) {}
}
```

After:

```php
use WorkOS\WorkOS;

class UserController
{
    public function __construct(private WorkOS $workos) {}
}
```

### Container resolution

Before:

```php
$service = app(WorkOSService::class);
// or
$service = app('workos'); // resolved to WorkOSService
```

After:

```php
$client = app(\WorkOS\WorkOS::class);
// or
$client = app('workos'); // resolved to \WorkOS\WorkOS
```

### Helper function

The `workos()` helper still works — its return type changed from `WorkOSService` to `\WorkOS\WorkOS`:

```php
$user = workos()->userManagement()->getUser($id);
```

---

## Service accessor renames

Several accessors were renamed upstream to align with the v5 SDK's service naming. Update any call sites:

| v5 (old)                                       | v6 (new)                          |
| ---------------------------------------------- | --------------------------------- |
| `workos()->mfa()`                              | `workos()->multiFactorAuth()`     |
| `workos()->portal()`                           | `workos()->adminPortal()`         |
| `workos()->rbac()`                             | `workos()->authorization()`       |
| `workos()->webhook()` (CRUD)                   | `workos()->webhooks()`            |
| `workos()->webhook()` (signature verification) | `workos()->webhookVerification()` |

The same renames apply to the `WorkOS` facade (e.g. `WorkOS::mfa()` → `WorkOS::multiFactorAuth()`).

Note that `webhook()` was split into two distinct services: `webhooks()` for CRUD operations and `webhookVerification()` for signature verification.

---

## Configuration changes

The `config/workos.php` file itself is unchanged — the same `api_key`, `client_id`, and `api_base_url` keys are recognized, with the same env var defaults (`WORKOS_API_KEY`, `WORKOS_CLIENT_ID`). **No changes to your published config file are required.**

Internally, the service provider no longer calls the removed static helpers `WorkOS::setApiKey()`, `WorkOS::setClientId()`, `WorkOS::setIdentifier()`, `WorkOS::setVersion()`, or `WorkOS::setApiBaseUrl()`. Config values are now passed directly to the `\WorkOS\WorkOS` constructor.

### Static readbacks no longer populated

If your application was reading configuration back from the global static state (e.g. `\WorkOS\WorkOS::getApiKey()`), **those values will now be `null`** — the service provider populates an instance, not the static registry. Read from Laravel config instead:

| Before                            | After                           |
| --------------------------------- | ------------------------------- |
| `\WorkOS\WorkOS::getApiKey()`     | `config('workos.api_key')`      |
| `\WorkOS\WorkOS::getClientId()`   | `config('workos.client_id')`    |
| `\WorkOS\WorkOS::getApiBaseUrl()` | `config('workos.api_base_url')` |

To find affected call sites in your application:

```bash
grep -rn 'WorkOS\\WorkOS::\(get\|set\)\(ApiKey\|ClientId\|ApiBaseUrl\|Identifier\|Version\)' app/ config/ routes/
```

---

## Facade

The `WorkOS` facade (aliased as `WorkOS\Laravel\Facades\WorkOS`) is unchanged in surface — all services are still accessed the same way:

```php
use WorkOS\Laravel\Facades\WorkOS;

$user = WorkOS::userManagement()->getUser($id);
```

Only the [renamed accessors](#service-accessor-renames) need updating. Return types in the facade's docblock have been updated to the new `\WorkOS\Service\*` namespaces from v5 — if your IDE flags type mismatches on renamed services, regenerate IDE helpers (e.g. `php artisan ide-helper:generate`).

---

## New services available

v6 exposes every service the v5 upstream SDK provides. In addition to the existing services, these are now available via the facade and client:

- `actions()`
- `apiKeys()`
- `connect()`
- `events()`
- `featureFlags()`
- `organizationDomains()`
- `pipes()`
- `pkce()`
- `radar()`
- `sessionManager()`
- `webhookVerification()`

See the upstream [v5 migration guide](https://github.com/workos/workos-php/blob/main/docs/V5_MIGRATION_GUIDE.md) for details on request/response model changes within each service.
