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
    /** @var Smarty */
    private $smarty;
    /** @var ResolverInterface|null */
    private $resolver = null;

    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
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
        }

        if (! $this->resolver) {
            throw new Exception\DomainException('No resolver provided for SmartyRenderer');
        }

        $file = $this->resolver->resolve($nameOrModel);

        if (! $file) {
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
        $template = $this->smarty->createTemplate('file:' . $file);
        $template->assign($values);

        return $template->fetch();
    }

    public function canRenderTrees(): bool
    {
        return true;
    }
}
