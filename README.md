# kaiseki/wp-two-stage-font-loader

Two-stage web font loading for WordPress: render `@font-face` CSS and preload links, toggling stage classes as fonts load.

Loads critical (stage 1) fonts first and the remaining (stage 2) fonts afterwards, adding a CSS class
to `<html>` at each stage so you can avoid layout shift and control the swap. Fonts are configured
declaratively and wired through `ConfigProvider` / `kaiseki/wp-hook`.

## Installation

```bash
composer require kaiseki/wp-two-stage-font-loader
```

Requires PHP 8.2 or newer.

## Usage

Register `ConfigProvider` with your laminas-style config aggregator and configure the `fonts` key.
Each font entry is a `FontConfig` (family, weight, style, the font files, preload flag, …) consumed by
`FontFactory`:

```php
use Kaiseki\WordPress\TwoStageFontLoader\FontManager;
use Kaiseki\WordPress\TwoStageFontLoader\Loader\ConfigLoader;

return [
    'fonts' => [
        // Critical fonts — loaded and preloaded first.
        'stage_1_fonts' => [
            [
                'family'  => 'Inter',
                'weight'  => '400',
                'files'   => [['url' => '/fonts/inter-400.woff2', 'format' => 'woff2']],
                'preload' => true,
            ],
        ],
        // Remaining fonts — loaded after stage 1.
        'stage_2_fonts' => [],
        'stage_1_class' => 'fonts-loaded-stage1',
        'stage_2_class' => 'fonts-loaded-stage2',
    ],
    'hook' => [
        'provider' => [
            FontManager::class,
            ConfigLoader::class,
        ],
    ],
];
```

`FontManager` resolves the appropriate hooks and renders the `@font-face` CSS, preload `<link>`s and
the small stage-swapping script; `ConfigLoader` turns the `fonts` config into `Font` objects and
registers them. The `stage_1_class` / `stage_2_class` are applied to the document as each stage loads.

## Development

```bash
composer install
composer check   # check-deps, cs-check, phpstan
```

## License

MIT — see [LICENSE](LICENSE).
