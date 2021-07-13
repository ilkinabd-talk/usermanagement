<?php
declare(strict_types=1);

/**
 * This file is part of the Vökuró.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Vokuro;

use Exception;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application as MvcApplication;
use Vokuro\Providers\DispatcherProvider;
use Vokuro\Providers\RouterProvider;

/**
 * Vökuró Application
 */
class Cli
{
    const APPLICATION_PROVIDER = 'bootstrap';

    /**
     * @var MvcApplication
     */
    protected $app;

    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * Project root path
     *
     * @var string
     */
    protected $rootPath;


    /**
     * Project root path
     *
     * @var string
     */
    protected $arguments;


    /**
     * @param string $rootPath
     *
     * @throws Exception
     */
    public function __construct(string $rootPath, $arguments)
    {
        $this->di   = new FactoryDefault\Cli();
        $dispatcher = new Dispatcher();
        $dispatcher->setDefaultNamespace('Vokuro\Tasks');
        $this->app       = $this->createApplication();
        $this->rootPath  = $rootPath;
        $this->arguments = $arguments;

        $this->di->setShared(self::APPLICATION_PROVIDER, $this);
        $this->di->setShared('dispatcher', $dispatcher);
        $this->initializeProviders();
    }

    /**
     * Run Vökuró Application
     *
     * @return void
     */
    public function run(): void
    {
        /** @var ResponseInterface $response */
        $this->app->handle($this->arguments);
    }

    /**
     * Get Project root path
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @return Console
     */
    protected function createApplication(): Console
    {
        return new Console($this->di);
    }

    /**
     * @throws Exception
     */
    protected function initializeProviders(): void
    {
        $filename = $this->rootPath . '/config/providers.php';
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception('File providers.php does not exist or is not readable.');
        }

        $providers = include_once $filename;
        foreach ($providers as $providerClass) {
            /** @var ServiceProviderInterface $provider */
            if ($providerClass !== DispatcherProvider::class && $providerClass !== RouterProvider::class) {
                $provider = new $providerClass;
                $provider->register($this->di);
            }
        }
    }
}
