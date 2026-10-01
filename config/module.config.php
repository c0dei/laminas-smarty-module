<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

return [
    'view_manager' => [
        'strategies' => [
            Strategy\SmartyStrategy::class,
        ],
    ],
    'smarty' => [
        'suffix' => 'tpl',
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            Renderer\SmartyRenderer::class => Renderer\SmartyRendererFactory::class,
            Strategy\SmartyStrategy::class => Strategy\SmartyStrategyFactory::class,
        ],
    ],
];
