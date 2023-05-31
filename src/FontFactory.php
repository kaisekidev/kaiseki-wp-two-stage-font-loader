<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Kaiseki\WordPress\TwoStageFontLoader\Exception\MissingArgumentException;

/**
 * @phpstan-type FontConfig array{
 *     family: string,
 *     src: list<string>,
 *     weight?: string,
 *     italic?: bool,
 *     style?: string
 * }
 */
final class FontFactory
{
    /**
     * @param FontConfig $config
     */
    public static function create(array $config): Font
    {
        self::validateConfig($config);

        $name = $config['family'];
        $urls = $config['src'];
        $weight = $config['weight'] ?? 'normal';
        $style = '';
        if (isset($config['italic']) && $config['italic'] === true) {
            $style = 'italic';
        } elseif (isset($config['style']) && $config['style'] !== '') {
            $style = $config['style'];
        }

        return new Font($name, $urls, $weight, $style);
    }

    /**
     * @param FontConfig $config
     */
    private static function validateConfig(array $config): void
    {
        $requiredFields = [
            'family',
            'src',
        ];

        foreach ($requiredFields as $key) {
            if (!isset($config[$key])) {
                throw new MissingArgumentException(
                    \Safe\sprintf(
                        'The given config <code>%s</code> is missing.',
                        $key
                    )
                );
            }
        }
    }
}
