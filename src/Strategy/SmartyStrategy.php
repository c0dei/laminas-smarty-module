<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\Strategy;

use C0dei\LaminasSmartyModule\Renderer\SmartyRenderer;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\View\Model\ModelInterface;
use Laminas\View\ViewEvent;

class SmartyStrategy extends AbstractListenerAggregate
{
    /** @var SmartyRenderer */
    private $renderer;

    /** @var string Suffix including the leading dot, e.g. ".tpl" */
    private $suffix;

    public function __construct(SmartyRenderer $renderer, string $suffix = 'tpl')
    {
        $this->renderer = $renderer;
        $this->suffix = '.' . ltrim($suffix, '.');
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
    }

    public function selectRenderer(ViewEvent $e): ?SmartyRenderer
    {
        $model = $e->getModel();

        if (! $model instanceof ModelInterface) {
            return null;
        }

        $template = $model->getTemplate();

        if (empty($template)) {
            return null;
        }

        if ($this->hasSuffix($template)) {
            return $this->renderer;
        }

        // e.g. a template map entry "layout/layout" => ".../layout.tpl"
        $resolver = $this->renderer->getResolver();
        if ($resolver) {
            $path = $resolver->resolve($template);
            if (is_string($path) && $this->hasSuffix($path)) {
                return $this->renderer;
            }
        }

        return null;
    }

    public function injectResponse(ViewEvent $e): void
    {
        if ($e->getRenderer() !== $this->renderer) {
            return;
        }

        $result = $e->getResult();

        if (! is_string($result)) {
            return;
        }

        $e->getResponse()->setContent($result);
    }

    private function hasSuffix(string $name): bool
    {
        return substr($name, -strlen($this->suffix)) === $this->suffix;
    }
}
