<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

use Psr\Container\ContainerInterface;

/**
 * Plain invokable factory: avoids FactoryInterface, whose container type
 * differs between laminas-servicemanager versions (Interop vs PSR).
 */
class ModuleOptionsFactory
{
    public function __invoke(ContainerInterface $container): ModuleOptions
    {
        $config = $container->get('config');
        return new ModuleOptions($config['smarty'] ?? []);
    }
}
