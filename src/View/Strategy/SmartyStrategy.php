<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule\View\Strategy;

use C0dei\LaminasSmartyModule\View\Renderer\SmartyRenderer;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\View\Model\ModelInterface;
use Laminas\View\ViewEvent;

class SmartyStrategy extends AbstractListenerAggregate
{
    /** @var SmartyRenderer */
    private $renderer;

    public function __construct(SmartyRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
    }

    public function selectRenderer(ViewEvent $e): ?SmartyRenderer
    {
        $model = $e->getModel();
        
        if (!$model instanceof ModelInterface) {
            return null;
        }

        $template = $model->getTemplate();

        if (empty($template)) {
            return null;
        }

        if (substr($template, -4) === '.tpl') {
            return $this->renderer;
        }

        $resolver = $this->renderer->getResolver();
        if ($resolver) {
            $path = $resolver->resolve($template);
            if ($path && substr($path, -4) === '.tpl') {
                return $this->renderer;
            }
        }

        return null;
    }

    public function injectResponse(ViewEvent $e): void
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }

        $result = $e->getResult();

        if (! is_string($result)) {
            return;
        }

        $response = $e->getResponse();
        $response->setContent($result);
    }
}
