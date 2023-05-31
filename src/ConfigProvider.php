<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Kaiseki\WordPress\TwoStageFontLoader\Loader\ConfigLoader;
use Kaiseki\WordPress\TwoStageFontLoader\Loader\ConfigLoaderFactory;

/**
 * @phpstan-type CustomImageSizeConfig array{0: string, 1: int, 2?: int, 3?: bool}
 */
final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'fonts' => [
                'stage_1_fonts' => [],
                'stage_2_fonts' => [],
                'stage_1_class' => 'fonts-loaded-stage1',
                'stage_2_class' => 'fonts-loaded-stage2',
            ],
            'dependencies' => [
                'factories' => [
                    ConfigLoader::class => ConfigLoaderFactory::class,
                ],
            ],
        ];
    }
}
