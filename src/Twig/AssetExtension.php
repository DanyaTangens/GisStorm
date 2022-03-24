<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private array $serverParams;
    private TwigFunctionFactory $twigFunctionFactory;

    public function __construct(
        array $serverParams,
        TwigFunctionFactory $twigFunctionFactory
    ) {
        $this->serverParams = $serverParams;
        $this->twigFunctionFactory = $twigFunctionFactory;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            $this->twigFunctionFactory->create('asset_url', [$this, 'getAssetUrl']),
            $this->twigFunctionFactory->create('url', [$this, 'getUrl']),
            $this->twigFunctionFactory->create('base_url', [$this, 'getBaseUrl']),
        ];
    }

    public function getAssetUrl(string $path): string
    {
        return $this->getBaseUrl() . $path;
    }

    public function getBaseUrl(): string
    {
        $scheme = $this->serverParams['REQUEST_SCHEME'] ?? 'http';
        return  $scheme . '://' . $this->serverParams['HTTP_HOST'] . '/';
    }

    public function getUrl(string $path): string
    {
        return $this->getBaseUrl() . $path;
    }
}