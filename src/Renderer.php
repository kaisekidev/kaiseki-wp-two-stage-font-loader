<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use function array_merge;
use function implode;
use function printf;

final class Renderer
{
    public function __construct(private readonly FontManager $fontManager)
    {
    }

    public function render(): void
    {
        $this->renderPreloaders();
        $this->renderStyleBlock();
        $this->renderScriptBlock();
    }

    private function getFontFaces(): string
    {
        $fontFaces = [];
        foreach (array_merge($this->fontManager->getStage1Fonts(), $this->fontManager->getStage2Fonts()) as $font) {
            $fontFace = (new FontFace($font))->getFontFaceString();
            if ($fontFace === '') {
                continue;
            }
            $fontFaces[] = $fontFace;
        }
        return implode('', $fontFaces);
    }

    /**
     * @param array<Font> $fonts
     */
    private function getFontSpecifications(array $fonts = []): string
    {
        $specifications = [];

        foreach ($fonts as $font) {
            $specifications[] = (new FontSpecification($font))->getFontSpecificationString();
        }
        return implode(', ', $specifications);
    }

    private function getPreloaders(): string
    {
        $preloaders = [];
        foreach ($this->fontManager->getStage1Fonts() as $font) {
            $preloader = (new Preloader($font))->getPreloaderString();
            if ($preloader === '') {
                continue;
            }

            $preloaders[] = $preloader;
        }
        return implode('', $preloaders);
    }

    private function renderPreloaders(): void
    {
        if ($this->fontManager->isPreloadersDisabled() === true) {
            return;
        }

        echo $this->getPreloaders();
    }

    private function renderScriptBlock(): void
    {
        $stage1Fonts = $this->fontManager->getStage1Fonts();
        $stage2Fonts = $this->fontManager->getStage2Fonts();
        $stage1Class = $this->fontManager->getStage1Class();
        $stage2Class = $this->fontManager->getStage2Class();
        if ($stage1Fonts === [] && $stage2Fonts === []) {
            return;
        }
        if ($stage1Fonts !== [] && $stage2Fonts === []) {
            $this->renderSingleStageScriptBlock($stage1Fonts, $stage1Class, $stage2Class);
            return;
        }
        if ($stage1Fonts === [] && $stage2Fonts !== []) {
            $this->renderSingleStageScriptBlock($stage2Fonts, $stage1Class, $stage2Class);
            return;
        }
        $this->renderTwoStageScriptBlock($stage1Fonts, $stage2Fonts, $stage1Class, $stage2Class);
    }

    /**
     * @param array<Font> $stage1Fonts
     * @param array<Font> $stage2Fonts
     */
    private function renderTwoStageScriptBlock(
        array $stage1Fonts,
        array $stage2Fonts,
        string $stage1Class,
        string $stage2Class
    ): void {
        echo "<script id=\"two-stage-font-loader\" type=\"text/javascript\"" . $this->getWpRocketDisableAttribute() . ">
         var fontsInStorage = sessionStorage.fsl1 && sessionStorage.fsl2;
         if (!fontsInStorage && 'fonts' in document) {
           function fetchFonts(t) {
             return Promise.all(t.map(function (t) {
               return document.fonts.load(t)
             }));
           }
           fetchFonts([" . $this->getFontSpecifications($stage1Fonts) . "]).then(function () {
             var e = document.documentElement;
             e.classList.add('" . $stage1Class . "');
             sessionStorage.fsl1 = !0;
             fetchFonts([" . $this->getFontSpecifications($stage2Fonts) . "]).then(function () {
               e.classList.add('" . $stage2Class . "');
               sessionStorage.fsl2 = !0;
             })
           })
         } else {
           var docEl = document.documentElement;
           docEl.classList.add('" . $stage1Class . "', '" . $stage2Class . "');
         }
        </script>";
    }

    /**
     * @param array<Font> $fonts
     */
    private function renderSingleStageScriptBlock(array $fonts, string $stage1Class, string $stage2Class): void
    {
        echo "<script id=\"two-stage-font-loader\" type=\"text/javascript\"" . $this->getWpRocketDisableAttribute() . ">
         var fontsInStorage = sessionStorage.fsl1 && sessionStorage.fsl2;
         if (!fontsInStorage && 'fonts' in document) {
           function fetchFonts(t) {
             return Promise.all(t.map(function (t) {
               return document.fonts.load(t)
             }));
           }
           fetchFonts([" . $this->getFontSpecifications($fonts) . "]).then(function () {
             var e = document.documentElement;
             sessionStorage.fsl1 = sessionStorage.fsl2 = !0;
             e.classList.add('" . $stage1Class . "', '" . $stage2Class . "');
           })
         } else {
           var docEl = document.documentElement;
           docEl.classList.add('" . $stage1Class . "', '" . $stage2Class . "');
         }
        </script>";
    }

    private function renderStyleBlock(): void
    {
        printf("<style>%s</style>\n", $this->getFontFaces());
    }

    private function getWpRocketDisableAttribute(): string
    {
        if (is_plugin_active('wp-rocket/wp-rocket.php')) {
            return ' data-nowprocket="true"';
        }
        return '';
    }
}
