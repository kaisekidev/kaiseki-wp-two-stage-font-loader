<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader\Loader;

use Kaiseki\WordPress\Hook\HookProviderInterface;
use Kaiseki\WordPress\TwoStageFontLoader\Font;
use Kaiseki\WordPress\TwoStageFontLoader\FontFactory;
use Kaiseki\WordPress\TwoStageFontLoader\FontManager;

use function add_action;
use function array_map;

/**
 * @phpstan-import-type FontConfig from FontFactory
 */
class ConfigLoader implements HookProviderInterface
{
    /**
     * @param list<FontConfig> $stageOneFonts
     * @param list<FontConfig> $stageTwoFonts
     * @param string           $stageOneClass
     * @param string           $stageTwoClass
     */
    public function __construct(
        private readonly array $stageOneFonts,
        private readonly array $stageTwoFonts,
        private readonly string $stageOneClass,
        private readonly string $stageTwoClass
    ) {
    }

    public function addHooks(): void
    {
        add_action(FontManager::ACTION_SETUP, [$this, 'registerFonts']);
    }

    public function registerFonts(FontManager $fontManager): void
    {
        $fontManager
            ->registerStage1Fonts(...$this->load($this->stageOneFonts))
            ->registerStage2Fonts(...$this->load($this->stageTwoFonts));
        $fontManager->setStage1Class($this->stageOneClass);
        $fontManager->setStage2Class($this->stageTwoClass);
    }

    /**
     * @param array<FontConfig> $data
     *
     * @return array<Font>
     */
    private function load(array $data): array
    {
        return array_map([FontFactory::class, 'create'], $data);
    }
}
