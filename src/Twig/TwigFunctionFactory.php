<?php

namespace App\Twig;

use Twig\TwigFunction;

class TwigFunctionFactory
{
    public function create(...$arguments): TwigFunction
    {
        return new TwigFunction(...$arguments);
    }
}