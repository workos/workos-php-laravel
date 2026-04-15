# Changelog

## [6.0.1](https://github.com/workos/workos-php-laravel/compare/v6.0.0...v6.0.1) (2026-04-15)


### Bug Fixes

* Remove extractVersion from matchUpdateTypes rules ([#95](https://github.com/workos/workos-php-laravel/issues/95)) ([665f3c4](https://github.com/workos/workos-php-laravel/commit/665f3c4b54e9f5f359836f206e2081c10d0eaf6d))


### Miscellaneous Chores

* **deps:** update minor and patch updates ([#91](https://github.com/workos/workos-php-laravel/issues/91)) ([bea62b5](https://github.com/workos/workos-php-laravel/commit/bea62b5cc1b4961c3cb9c46e9a32241c418cd940))
* do not include whole name ([7678389](https://github.com/workos/workos-php-laravel/commit/7678389d73f5b7d4b291dd20b8409acf757a11b3))

## [6.0.0](https://github.com/workos/workos-php-laravel/compare/workos/workos-php-laravel-v5.1.0...workos/workos-php-laravel-v6.0.0) (2026-04-14)


### ⚠ BREAKING CHANGES

* v6 is a major rewrite of the Laravel SDK to align with the redesigned `workos/workos-php` v5 client.
* `WorkOS\Laravel\Services\WorkOSService` has been removed. The container, helper, and facade now resolve to `\WorkOS\WorkOS` directly.
* Several service accessors were renamed upstream: `mfa()` → `multiFactorAuth()`, `portal()` → `adminPortal()`, `rbac()` → `authorization()`, and `webhook()` was split into `webhooks()` and `webhookVerification()`.
* Before upgrading, follow `docs/V6_MIGRATION_GUIDE.md`.

### Features

* rewrite the Laravel integration as a thin adapter over `workos/workos-php` v5, bind `\WorkOS\WorkOS` directly, and expose the upstream v5 service surface ([#87](https://github.com/workos/workos-php-laravel/issues/87)) ([b72cb58](https://github.com/workos/workos-php-laravel/commit/b72cb58803a14826a0daeec6b2d2adec549c69eb))

## [5.1.0](https://github.com/workos/workos-php-laravel/compare/workos/workos-php-laravel-v5.0.1...workos/workos-php-laravel-v5.1.0) (2026-03-20)


### Features

* add Facade, improve performance and ergonomics ([#69](https://github.com/workos/workos-php-laravel/issues/69)) ([32cae0f](https://github.com/workos/workos-php-laravel/commit/32cae0fe8567fae1891e71f2c4a1bbbffd48a9db))


### Bug Fixes

* update renovate rules ([#78](https://github.com/workos/workos-php-laravel/issues/78)) ([2fbd084](https://github.com/workos/workos-php-laravel/commit/2fbd0849596b2b6dd07a103becc467876ce94a28))


### Miscellaneous Chores

* Add DX as Codeowners ([#81](https://github.com/workos/workos-php-laravel/issues/81)) ([bea5968](https://github.com/workos/workos-php-laravel/commit/bea596841fb57b7b66adc0b95366c8f2e691d882))
* **deps:** update actions/checkout action to v6 ([#74](https://github.com/workos/workos-php-laravel/issues/74)) ([1140490](https://github.com/workos/workos-php-laravel/commit/1140490d79a4186ff7a8d818c97211fff2287374))
* **deps:** update actions/create-github-app-token action to v3 ([#84](https://github.com/workos/workos-php-laravel/issues/84)) ([400e880](https://github.com/workos/workos-php-laravel/commit/400e880dae8a5c6c06b5edf1e22c35d6b41a53e0))
* **deps:** update amannn/action-semantic-pull-request action to v6 ([#85](https://github.com/workos/workos-php-laravel/issues/85)) ([345ca71](https://github.com/workos/workos-php-laravel/commit/345ca71443287fc45c8f9c3a3995950a79755be9))
* Pin GitHub Actions ([#79](https://github.com/workos/workos-php-laravel/issues/79)) ([2a88f7b](https://github.com/workos/workos-php-laravel/commit/2a88f7b59f54edafc089e511504b30101af7697d))
