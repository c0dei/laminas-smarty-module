<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\Renderer;

use C0dei\LaminasSmartyModule\ModuleOptions;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Resolver\ResolverInterface;
use Psr\Container\ContainerInterface;
use Smarty\Smarty;

class SmartyRendererFactory
{
    public function __invoke(ContainerInterface $container): SmartyRenderer
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $smarty = new Smarty();

        $templateDir = $moduleOptions->getTemplateDir();
        if (! $templateDir) {
            $config = $container->has('config') ? $container->get('config') : [];
            $templateDir = array_values($config['view_manager']['template_path_stack'] ?? []);
        }
        if ($templateDir) {
            $smarty->setTemplateDir($templateDir);
        }

        $smarty->setCompileDir($this->ensureDirectory($moduleOptions->getCompileDir()));
        $smarty->setCacheDir($this->ensureDirectory($moduleOptions->getCacheDir()));
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

    private function ensureDirectory(string $dir): string
    {
        if (! is_dir($dir) && ! @mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new RuntimeException(sprintf('Unable to create Smarty directory "%s"', $dir));
        }

        return $dir;
    }
}
