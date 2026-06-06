<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

return [
    'view_manager' => [
        'smarty_default_suffix' => 'tpl',
        'strategies' => [
            View\Strategy\SmartyStrategy::class,
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
            View\Renderer\SmartyRenderer::class => View\Renderer\SmartyRendererFactory::class,
            View\Strategy\SmartyStrategy::class => View\Strategy\SmartyStrategyFactory::class,
        ],
    ],
];
