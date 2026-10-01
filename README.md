# Laminas Smarty 5 Module

This module provides Smarty 5 integration for Laminas MVC applications.

## Requirements

- PHP 7.2 or higher (including PHP 8.x)
- Laminas MVC 3.1+ / laminas-view 2.11+
- Smarty 5

Composer picks the Laminas versions matching your PHP version
(e.g. laminas-mvc 3.1 / laminas-view 2.11 on PHP 7.2).

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

You can override Smarty settings in your application's `config/autoload/global.php`
(see `config/laminas-smarty-module.config.php.dist`):

```php
return [
    'smarty' => [
        'suffix'       => 'tpl',                  // templates ending in .tpl are rendered by Smarty
        'template_dir' => [],                     // defaults to view_manager.template_path_stack
        'compile_dir'  => 'data/Smarty/compile',
        'cache_dir'    => 'data/Smarty/cache',
        'caching'      => \Smarty\Smarty::CACHING_OFF,
        'escape_html'  => true,
    ],
];
```

`compile_dir` and `cache_dir` are created automatically; an exception is thrown
if that fails.

## Usage

In your controllers, return a ViewModel as usual and specify a `.tpl` template:

```php
use Laminas\View\Model\ViewModel;

public function indexAction()
{
    $view = new ViewModel(['name' => 'World']);
    $view->setTemplate('application/index/index.tpl');
    return $view;
}
```

Templates are selected when the template name ends with the configured suffix,
or when it resolves (e.g. through `template_map`) to a file with that suffix.

### Layouts

Layouts may be `.phtml` or `.tpl`. Child view models are rendered first and
passed to the layout as variables, so a Smarty layout prints them with `{$content}`.
Because `escape_html` is enabled by default, use `nofilter` for already-rendered HTML:

```smarty
<body>{$content nofilter}</body>
```

### Includes

`{include}` and `{extends}` resolve against `template_dir`, which defaults to
your `view_manager.template_path_stack`:

```smarty
{include file="application/partial/menu.tpl"}
```

### Escaping

`escape_html` is enabled by default, so `{$var}` is HTML-escaped. Use
`{$var nofilter}` to print trusted HTML as-is.

### Caching

Even when `caching` is enabled, output is cached only for view models that set a
cache id, so pages rendered with different variables never share a cache entry:

```php
use C0dei\LaminasSmartyModule\Renderer\SmartyRenderer;

$view->setOption(SmartyRenderer::OPTION_CACHE_ID, 'product-' . $id);
```

`SmartyRenderer::OPTION_COMPILE_ID` sets the Smarty compile id the same way.

### Accessing Smarty

The `Smarty\Smarty` instance is available from the renderer, e.g. to register plugins:

```php
$smarty = $container->get(\C0dei\LaminasSmartyModule\Renderer\SmartyRenderer::class)->getEngine();
```

## Development

```bash
composer install
composer test
composer cs:check
composer phpstan
```
