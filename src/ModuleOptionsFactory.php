<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ModuleOptions
    {
        $config = $container->get('config');
        return new ModuleOptions($config['smarty'] ?? []);
    }
}
