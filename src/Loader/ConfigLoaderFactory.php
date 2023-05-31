<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader\Loader;

use Kaiseki\Config\Config;
use Kaiseki\WordPress\TwoStageFontLoader\FontFactory;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-import-type FontConfig from FontFactory
 */
final class ConfigLoaderFactory
{
    public function __invoke(ContainerInterface $container): ConfigLoader
    {
        $config = Config::get($container);
        /** @var list<FontConfig> $stage1Fonts */
        $stage1Fonts = $config->array('fonts/stage_1_fonts', []);
        /** @var list<FontConfig> $stage2Fonts */
        $stage2Fonts = $config->array('fonts/stage_2_fonts', []);
        return new ConfigLoader(
            $stage1Fonts,
            $stage2Fonts,
            $config->string('fonts/stage_1_class', 'fonts-loaded-stage1'),
            $config->string('fonts/stage_2_class', 'fonts-loaded-stage2')
        );
    }
}
