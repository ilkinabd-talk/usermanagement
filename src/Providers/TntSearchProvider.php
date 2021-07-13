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

namespace Vokuro\Providers;

use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use TeamTNT\TNTSearch\Stemmer\PorterStemmer;
use TeamTNT\TNTSearch\TNTSearch;
use Vokuro\Application;

class TntSearchProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'search';

    /**
     * @param DiInterface $di
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */
        $config = $di->getShared('config');

        /** @var Config $dbConfig */
        $dbConfig = $config->path('database');

        $di->setShared(
            $this->providerName,
            function () use ($dbConfig, $di) {
                $tnt = new TNTSearch;
                /** @var Application $app */
                $app = $di->get(Application::APPLICATION_PROVIDER);
                $tnt->loadConfig([
                    'driver'   => $dbConfig['adapter'],
                    'host'     => $dbConfig['host'],
                    'database' => $dbConfig['dbname'],
                    'username' => $dbConfig['username'],
                    'password' => $dbConfig['password'],
                    'storage'  => $app->getRootPath() . '/var/search',
                    'stemmer'  => PorterStemmer::class,
                ]);
                return $tnt;
            }
        );
    }
}
