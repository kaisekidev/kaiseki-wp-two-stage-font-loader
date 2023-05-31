<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use function in_array;

final class FontSpecification
{
    public function __construct(private readonly Font $font)
    {
    }

    public function getFontSpecificationString(): string
    {
        $fontSpecification = "'";

        if ($this->font->style !== '') {
            $fontSpecification .= $this->font->style . ' ';
        }
        if (!in_array($this->font->weight, ['400', 'normal'], true)) {
            $fontSpecification .= $this->font->weight . ' ';
        }

        return $fontSpecification . '1em "' . $this->font->name . "\"'";
    }
}
