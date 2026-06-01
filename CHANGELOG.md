# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0 - 2026-06-01

First tagged release.

### Added

- Two-stage font loading via `FontManager` + `ConfigLoader` hook providers: renders `@font-face` CSS,
  preload links and a stage-swapping script for `stage_1_fonts` / `stage_2_fonts`, applying the
  configured `stage_1_class` / `stage_2_class`. Driven by the `fonts` config key (`ConfigProvider`,
  `ConfigLoaderFactory`, `FontFactory`).

### Changed

- **BC:** adopted the `kaiseki/wp-hook` 2.0 API — `FontManager` and `ConfigLoader` implement
  `HookProviderInterface` / `addHooks()` (was `HookCallbackProviderInterface::registerHookCallbacks()`).
- **BC:** updated for `kaiseki/config` 2.0 — `ConfigLoaderFactory` uses `Config::fromContainer()` and
  the `.` config-key delimiter (`fonts.stage_1_fonts` etc., was `fonts/…`).
- PHP requirement is `^8.2` (PHP 8.4 is the primary target); `thecodingmachine/safe` bumped to `^2.0`
  (`inpsyde/wp-context ^1.5` already supports 8.4); `kaiseki/config` + `kaiseki/wp-hook` pinned to `^2.0`.
- Converted the toolchain from PHP_CodeSniffer to the shared `kaiseki/php-coding-standard` php-cs-fixer
  config; PHPStan 2 / PHPUnit 11 / composer-require-checker 4; added the reusable-workflow CI caller,
  `dependabot.yml` and `update-changelog.yml` (the repo had no `.github/`).

### Fixed

- PHPStan 2 (level max): removed a redundant `&& $stage2Fonts !== []` branch in
  `Renderer::renderScriptBlock()` (it was provably always true after the preceding guards). No
  behaviour change.
