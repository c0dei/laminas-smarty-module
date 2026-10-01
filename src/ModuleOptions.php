<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModule;

use Laminas\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * Template suffix for Smarty files (without the leading dot)
     * @var string
     */
    protected $suffix = 'tpl';

    /**
     * Directories Smarty searches for {include} / {extends}.
     * When empty, view_manager.template_path_stack is used.
     * @var string[]
     */
    protected $templateDir = [];

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
     * Smarty caching mode (Smarty::CACHING_OFF, CACHING_LIFETIME_CURRENT, ...)
     * @var int
     */
    protected $caching = 0;

    /**
     * Auto-escape HTML in {$var} output
     * @var bool
     */
    protected $escapeHtml = true;

    public function setSuffix(string $suffix): self
    {
        $this->suffix = ltrim($suffix, '.');
        return $this;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string|string[] $templateDir
     */
    public function setTemplateDir($templateDir): self
    {
        $this->templateDir = array_values((array) $templateDir);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTemplateDir(): array
    {
        return $this->templateDir;
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

    /**
     * @param int|bool $caching
     */
    public function setCaching($caching): self
    {
        $this->caching = (int) $caching;
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
