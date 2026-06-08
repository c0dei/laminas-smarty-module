<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

return [
    'view_manager' => [
        'smarty_default_suffix' => 'tpl',
        'strategies' => [
            Strategy\SmartyStrategy::class,
        ],
    ],
    'smarty' => [
        'suffix' => 'tpl',
        // Optional configuration for Smarty class
        // 'compile_dir' => 'data/Smarty/compile',
        // 'cache_dir' => 'data/Smarty/cache',
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            Renderer\SmartyRenderer::class => Renderer\SmartyRendererFactory::class,
            Strategy\SmartyStrategy::class => Strategy\SmartyStrategyFactory::class,
        ],
    ],
];
