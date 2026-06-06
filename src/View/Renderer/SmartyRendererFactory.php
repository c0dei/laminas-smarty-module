<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\View\Renderer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Resolver\ResolverInterface;
use Smarty\Smarty;

class SmartyRendererFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SmartyRenderer
    {
        $config = $container->get('config');
        $smartyConfig = $config['smarty'] ?? [];

        $smarty = new Smarty();

        if (isset($smartyConfig['compile_dir'])) {
            $smarty->setCompileDir($smartyConfig['compile_dir']);
        } else {
            // Default compile dir
            $compileDir = 'data/Smarty/compile';
            if (!is_dir($compileDir)) {
                @mkdir($compileDir, 0775, true);
            }
            $smarty->setCompileDir($compileDir);
        }

        if (isset($smartyConfig['cache_dir'])) {
            $smarty->setCacheDir($smartyConfig['cache_dir']);
        } else {
            $cacheDir = 'data/Smarty/cache';
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0775, true);
            }
            $smarty->setCacheDir($cacheDir);
        }

        if (isset($smartyConfig['caching'])) {
            $smarty->setCaching((int)$smartyConfig['caching']);
        }
        
        if (isset($smartyConfig['escape_html'])) {
            $smarty->setEscapeHtml((bool)$smartyConfig['escape_html']);
        }

        $renderer = new SmartyRenderer($smarty);

        if ($container->has('ViewResolver')) {
            $resolver = $container->get('ViewResolver');
            if ($resolver instanceof ResolverInterface) {
                $renderer->setResolver($resolver);
            }
        }

        return $renderer;
    }
}
