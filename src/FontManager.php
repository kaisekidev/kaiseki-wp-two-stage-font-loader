<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Kaiseki\WordPress\Hook\HookCallbackProviderInterface;

use function array_merge;
use function array_values;

final class FontManager implements HookCallbackProviderInterface
{
    public const ACTION_SETUP = 'woda_fontloader_setup';

    /** @var array<string, bool> */
    private array $hooksAdded = [];
    private FontHookResolver $hookResolver;
    private Renderer $renderer;
    private bool $setupDone = false;
    private bool $isPreloadersDisabled = false;
    private string $stage1Class = 'fonts-loaded-stage1';
    private string $stage2Class = 'fonts-loaded-stage2';
    /** @var list<Font> */
    private array $stage1Fonts = [];
    /** @var list<Font> */
    private array $stage2Fonts = [];

    public function __construct(FontHookResolver $hookResolver)
    {
        $this->renderer = new Renderer($this);
        $this->hookResolver = $hookResolver;
    }

    public function registerHookCallbacks(): void
    {
        $this->setup();
    }

    public function registerStage1Fonts(Font ...$fonts): FontManager
    {
        $this->stage1Fonts = array_values(array_merge($this->stage1Fonts, $fonts));
        return $this;
    }

    public function registerStage2Fonts(Font ...$fonts): FontManager
    {
        $this->stage2Fonts = array_values(array_merge($this->stage2Fonts, $fonts));
        return $this;
    }

    public function setup(): bool
    {
        $hooksAdded = 0;

        foreach ($this->hookResolver->resolve() as $hook) {
            if (
                (
                    isset($this->hooksAdded[$hook])
                    && $this->hooksAdded[$hook] === true
                )
                || (
                    did_action($hook) > 0
                    && doing_action($hook) === false
                )
            ) {
                continue;
            }

            $hooksAdded++;
            $this->hooksAdded[$hook] = true;

            add_action($hook, function (): void {
                $this->ensureSetup();
                $this->renderer->render();
            });
        }

        return $hooksAdded > 0;
    }

    public function setStage1Class(string $class): FontManager
    {
        $this->stage1Class = $class;
        return $this;
    }

    public function setStage2Class(string $class): FontManager
    {
        $this->stage2Class = $class;
        return $this;
    }

    public function disablePreloaders(): FontManager
    {
        $this->isPreloadersDisabled = true;
        return $this;
    }

    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    public function getStage1Class(): string
    {
        return $this->stage1Class;
    }

    public function getStage2Class(): string
    {
        return $this->stage2Class;
    }

    /**
     * @return array<Font>
     */
    public function getStage1Fonts(): array
    {
        return $this->stage1Fonts;
    }

    /**
     * @return array<Font>
     */
    public function getStage2Fonts(): array
    {
        return $this->stage2Fonts;
    }

    public function isPreloadersDisabled(): bool
    {
        return $this->isPreloadersDisabled;
    }

    /**
     * @return void
     */
    private function ensureSetup(): void
    {
        if ($this->setupDone === true) {
            return;
        }

        $this->setupDone = true;

        $lastHook = $this->hookResolver->lastHook();

        // We should not setup if there's no hook or last hook already fired.
        if ($lastHook !== null && did_action($lastHook) > 0 && doing_action($lastHook) === false) {
            return;
        }

        do_action(self::ACTION_SETUP, $this);
    }
}
