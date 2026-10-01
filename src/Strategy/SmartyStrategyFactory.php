<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\Strategy;

use C0dei\LaminasSmartyModule\Renderer\SmartyRenderer;
use Psr\Container\ContainerInterface;

class SmartyStrategyFactory
{
    public function __invoke(ContainerInterface $container): SmartyStrategy
    {
        return new SmartyStrategy($container->get(SmartyRenderer::class));
    }
}
