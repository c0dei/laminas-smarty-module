<?php

declare(strict_types=1);

namespace C0dei\LaminasSmartyModuleTest;

use C0dei\LaminasSmartyModule\Module;
use C0dei\LaminasSmartyModule\Renderer\SmartyRenderer;
use C0dei\LaminasSmartyModule\Strategy\SmartyStrategy;
use Laminas\Http\Response;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplatePathStack;
use Laminas\View\Strategy\PhpRendererStrategy;
use Laminas\View\View;
use PHPUnit\Framework\TestCase;
use Smarty\Smarty;

class IntegrationTest extends TestCase
{
    /** @var string */
    private $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/laminas-smarty-module-test-' . uniqid('', true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->tmpDir);
    }

    /**
     * @param array<string, mixed> $smartyConfig
     */
    private function createContainer(array $smartyConfig = [], bool $withViewRenderer = true): ServiceManager
    {
        $viewDir = __DIR__ . '/fixtures/view';
        $moduleConfig = (new Module())->getConfig();

        $container = new ServiceManager($moduleConfig['service_manager']);
        $container->setService('config', [
            'view_manager' => ['template_path_stack' => [$viewDir]],
            'smarty' => $smartyConfig + $moduleConfig['smarty'] + [
                'compile_dir' => $this->tmpDir . '/compile',
                'cache_dir' => $this->tmpDir . '/cache',
            ],
        ]);
        $resolver = new TemplatePathStack(['script_paths' => [$viewDir]]);
        $container->setService('ViewResolver', $resolver);

        if ($withViewRenderer) {
            $phpRenderer = new PhpRenderer();
            $phpRenderer->setResolver($resolver);
            $container->setService('ViewRenderer', $phpRenderer);
        }

        return $container;
    }

    /**
     * Mirrors laminas-mvc's ViewManager: PhpRenderer as default strategy,
     * configured strategies attached at priority 100.
     *
     * @param array<string, mixed> $smartyConfig
     */
    private function render(ViewModel $model, array $smartyConfig = []): string
    {
        $container = $this->createContainer($smartyConfig);
        $phpRenderer = $container->get('ViewRenderer');

        $response = new Response();
        $view = new View();
        $view->setResponse($response);
        (new PhpRendererStrategy($phpRenderer))->attach($view->getEventManager());
        $container->get(SmartyStrategy::class)->attach($view->getEventManager(), 100);

        $view->render($model);

        return $response->getContent();
    }

    private function model(string $template, array $variables = []): ViewModel
    {
        $model = new ViewModel($variables);
        $model->setTemplate($template);
        return $model;
    }

    public function testSmartyLayoutRendersChildContent(): void
    {
        $layout = $this->model('layout.tpl');
        $layout->addChild($this->model('index.tpl', ['name' => 'World']));

        self::assertSame("<html>[Hello World\n]</html>\n", $this->render($layout));
    }

    public function testPhpLayoutRendersSmartyChild(): void
    {
        $layout = $this->model('layout.phtml');
        $layout->addChild($this->model('index.tpl', ['name' => 'World']));

        self::assertSame("<html>[Hello World\n]</html>\n", $this->render($layout));
    }

    public function testTemplateWithoutSuffixRendersTpl(): void
    {
        self::assertSame("Hello World\n", $this->render($this->model('index', ['name' => 'World'])));
    }

    public function testTplTakesPrecedenceOverPhtmlWithoutSuffix(): void
    {
        self::assertSame("Smarty World\n", $this->render($this->model('both', ['name' => 'World'])));
    }

    public function testTemplateWithoutTplFallsBackToPhpRenderer(): void
    {
        self::assertSame("PHP only World", $this->render($this->model('only-php', ['name' => 'World'])));
    }

    public function testExplicitPhtmlSuffixUsesPhpRenderer(): void
    {
        self::assertSame("PHP World", $this->render($this->model('both.phtml', ['name' => 'World'])));
    }

    public function testIncludeResolvesAgainstTemplatePathStack(): void
    {
        self::assertSame("P:1\n", $this->render($this->model('include.tpl')));
    }

    public function testSuffixOptionSelectsRenderer(): void
    {
        $output = $this->render($this->model('index.html', ['name' => '<b>']), ['suffix' => 'html']);

        self::assertSame("Hello &lt;b&gt;\n", $output);
    }

    public function testTplIsNotSelectedWhenSuffixDiffers(): void
    {
        $container = $this->createContainer(['suffix' => 'html']);
        $strategy = $container->get(SmartyStrategy::class);

        $event = new \Laminas\View\ViewEvent();
        $event->setModel($this->model('layout.phtml'));
        self::assertNull($strategy->selectRenderer($event));
    }

    public function testEscapeHtmlIsEnabledByDefault(): void
    {
        self::assertSame("Hello &lt;script&gt;\n", $this->render($this->model('index.tpl', ['name' => '<script>'])));
    }

    public function testCachingIsSkippedWithoutCacheId(): void
    {
        $renderer = $this->createContainer(['caching' => true])->get(SmartyRenderer::class);

        self::assertSame("Hello alice\n", $renderer->render($this->model('index.tpl', ['name' => 'alice'])));
        self::assertSame("Hello bob\n", $renderer->render($this->model('index.tpl', ['name' => 'bob'])));
    }

    public function testCachingIsKeyedByCacheId(): void
    {
        $renderer = $this->createContainer(['caching' => Smarty::CACHING_LIFETIME_CURRENT])
            ->get(SmartyRenderer::class);

        $render = function (string $cacheId, string $name) use ($renderer): string {
            $model = $this->model('index.tpl', ['name' => $name]);
            $model->setOption(SmartyRenderer::OPTION_CACHE_ID, $cacheId);
            return $renderer->render($model);
        };

        self::assertSame("Hello alice\n", $render('user-1', 'alice'));
        self::assertSame("Hello bob\n", $render('user-2', 'bob'));
        self::assertSame("Hello alice\n", $render('user-1', 'changed'));
    }

    public function testViewHelpersAreAvailableAsThis(): void
    {
        $container = $this->createContainer();
        $container->get('ViewRenderer')->plugin('basePath')->setBasePath('/app');

        $output = $container->get(SmartyRenderer::class)->render($this->model('helper.tpl'));

        self::assertSame("/app/css/a.css|<title>Hi</title>\n", $output);
    }

    public function testWorksWithoutViewRenderer(): void
    {
        $renderer = $this->createContainer([], false)->get(SmartyRenderer::class);

        self::assertSame("Hello World\n", $renderer->render($this->model('index.tpl', ['name' => 'World'])));
    }

    public function testCreatesCompileAndCacheDirectories(): void
    {
        $this->createContainer()->get(SmartyRenderer::class);

        self::assertDirectoryExists($this->tmpDir . '/compile');
        self::assertDirectoryExists($this->tmpDir . '/cache');
    }

    private function removeDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }
}
