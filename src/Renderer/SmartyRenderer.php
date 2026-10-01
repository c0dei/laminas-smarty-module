<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\Renderer;

use Laminas\View\Exception;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\Renderer\TreeRendererInterface;
use Laminas\View\Resolver\ResolverInterface;
use Laminas\View\Variables;
use Smarty\Smarty;

class SmartyRenderer implements RendererInterface, TreeRendererInterface
{
    /**
     * ViewModel option holding the Smarty cache_id. Output is cached only
     * when this is set, so pages with different variables never share a cache.
     */
    public const OPTION_CACHE_ID = 'smarty_cache_id';

    /** ViewModel option holding the Smarty compile_id. */
    public const OPTION_COMPILE_ID = 'smarty_compile_id';

    /** @var Smarty */
    private $smarty;
    /** @var ResolverInterface|null */
    private $resolver = null;
    /** @var string Suffix including the leading dot, e.g. ".tpl" */
    private $suffix;

    public function __construct(Smarty $smarty, string $suffix = 'tpl')
    {
        $this->smarty = $smarty;
        $this->suffix = '.' . ltrim($suffix, '.');
    }

    public function getEngine(): Smarty
    {
        return $this->smarty;
    }

    public function setResolver(ResolverInterface $resolver): self
    {
        $this->resolver = $resolver;
        return $this;
    }

    public function getResolver(): ?ResolverInterface
    {
        return $this->resolver;
    }

    public function render($nameOrModel, $values = null): string
    {
        $cacheId = null;
        $compileId = null;

        if ($nameOrModel instanceof ModelInterface) {
            $model = $nameOrModel;
            $nameOrModel = $model->getTemplate();

            if (empty($nameOrModel)) {
                throw new Exception\DomainException(sprintf(
                    '%s: received View Model argument, but template is empty',
                    __METHOD__
                ));
            }
            $values = $model->getVariables();
            $cacheId = $model->getOption(self::OPTION_CACHE_ID);
            $compileId = $model->getOption(self::OPTION_COMPILE_ID);
        }

        if (! $this->resolver) {
            throw new Exception\DomainException('No resolver provided for SmartyRenderer');
        }

        $file = $this->resolveFile($nameOrModel);

        if ($file === null) {
            throw new Exception\DomainException(sprintf(
                '%s: could not resolve template "%s" to a file',
                __METHOD__,
                $nameOrModel
            ));
        }

        if ($values instanceof Variables) {
            $values = $values->getArrayCopy();
        } elseif ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        } elseif (! is_array($values)) {
            $values = [];
        }

        // Isolate data for this specific template rendering
        $template = $this->smarty->createTemplate('file:' . $file, $cacheId, $compileId);
        if ($cacheId === null) {
            $template->setCaching(Smarty::CACHING_OFF);
        }
        $template->assign($values);

        return $template->fetch();
    }

    /**
     * Whether $name resolves to a Smarty template: either it ends with the
     * suffix, or "$name.<suffix>" exists, or it resolves (e.g. through a
     * template map) to a file with the suffix.
     */
    public function canRender(string $name): bool
    {
        if ($this->hasSuffix($name)) {
            return true;
        }

        $file = $this->resolveFile($name);
        return $file !== null && $this->hasSuffix($file);
    }

    /**
     * Names without the suffix are tried as "$name.<suffix>" first, so
     * "application/index/index" renders index.tpl when it exists.
     */
    private function resolveFile(string $name): ?string
    {
        if (! $this->resolver) {
            return null;
        }

        if (! $this->hasSuffix($name)) {
            $file = $this->resolver->resolve($name . $this->suffix);
            if (is_string($file) && $file !== '') {
                return $file;
            }
        }

        $file = $this->resolver->resolve($name);
        return is_string($file) && $file !== '' ? $file : null;
    }

    private function hasSuffix(string $name): bool
    {
        return substr($name, -strlen($this->suffix)) === $this->suffix;
    }

    /**
     * Children are rendered by Laminas\View\View and passed in as variables
     * (e.g. {$content}), since Smarty has no notion of view model trees.
     */
    public function canRenderTrees(): bool
    {
        return false;
    }
}
