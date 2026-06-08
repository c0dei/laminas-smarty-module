<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\Renderer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Resolver\ResolverInterface;
use Smarty\Smarty;

class SmartyRendererFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SmartyRenderer
    {
        /** @var \C0dei\LaminasSmartyModule\ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(\C0dei\LaminasSmartyModule\ModuleOptions::class);

        $smarty = new Smarty();

        $compileDir = $moduleOptions->getCompileDir();
        if (!is_dir($compileDir)) {
            @mkdir($compileDir, 0775, true);
        }
        $smarty->setCompileDir($compileDir);

        $cacheDir = $moduleOptions->getCacheDir();
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }
        $smarty->setCacheDir($cacheDir);

        $smarty->setCaching($moduleOptions->getCaching());
        $smarty->setEscapeHtml($moduleOptions->getEscapeHtml());

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
