<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\View\Strategy;

use C0dei\LaminasSmartyModule\View\Renderer\SmartyRenderer;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SmartyStrategyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SmartyStrategy
    {
        $renderer = $container->get(SmartyRenderer::class);
        return new SmartyStrategy($renderer);
    }
}
