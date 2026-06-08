<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * Template suffix for Smarty files
     * @var string
     */
    protected $suffix = 'tpl';

    /**
     * Directory for compiled templates
     * @var string
     */
    protected $compileDir = 'data/Smarty/compile';

    /**
     * Directory for cached templates
     * @var string
     */
    protected $cacheDir = 'data/Smarty/cache';

    /**
     * Enable caching
     * @var int
     */
    protected $caching = 0;

    /**
     * Auto-escape HTML
     * @var bool
     */
    protected $escapeHtml = false;

    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;
        return $this;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setCompileDir(string $compileDir): self
    {
        $this->compileDir = $compileDir;
        return $this;
    }

    public function getCompileDir(): string
    {
        return $this->compileDir;
    }

    public function setCacheDir(string $cacheDir): self
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function setCaching(int $caching): self
    {
        $this->caching = $caching;
        return $this;
    }

    public function getCaching(): int
    {
        return $this->caching;
    }

    public function setEscapeHtml(bool $escapeHtml): self
    {
        $this->escapeHtml = $escapeHtml;
        return $this;
    }

    public function getEscapeHtml(): bool
    {
        return $this->escapeHtml;
    }
}
