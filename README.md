# Laminas Smarty 5 Module

This module provides Smarty 5 integration for Laminas MVC applications.

## Requirements

- PHP 7.2 or higher (up to PHP 8.x)
- Laminas MVC
- Smarty 5

## Installation

You can install this module using Composer:

```bash
composer require c0dei/laminas-smarty-module
```

*(Note: Until published on Packagist, you can use composer's `path` or `vcs` repository features).*

## Configuration

Add the module to your `config/modules.config.php`:

```php
return [
    // ... other modules
    'C0dei\LaminasSmartyModule',
];
```

By default, the module expects template files to end with `.tpl`. You can override Smarty settings in your application's `config/autoload/global.php` or `module.config.php`:

```php
return [
    'smarty' => [
        'suffix' => 'tpl',
        'compile_dir' => 'data/Smarty/compile',
        'cache_dir' => 'data/Smarty/cache',
        'caching' => 0,
        'escape_html' => false,
    ],
];
```

## Usage

In your controllers, simply return a ViewModel as usual, and specify a `.tpl` template:

```php
use Laminas\View\Model\ViewModel;

public function indexAction()
{
    $view = new ViewModel(['name' => 'World']);
    $view->setTemplate('application/index/index.tpl');
    return $view;
}
```

If you configure your `template_map` or `template_path_stack` properly to resolve `.tpl` files, the Smarty strategy will automatically pick them up and render them using the Smarty 5 engine.
