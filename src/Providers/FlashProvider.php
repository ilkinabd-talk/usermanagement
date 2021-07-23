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

use Phalcon\Escaper;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Flash\Direct as Flash;

class FlashProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'flash';

    /**
     * @param  DiInterface  $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $di->set(
            $this->providerName,
            function () {
                $escaper = new Escaper();
                $flash   = new Flash($escaper);
                $flash->setImplicitFlush(false);
                $flash->setCssClasses(
                    [
                        'error'   => 'alert alert-danger-soft mb-2 show',
                        'success' => 'alert alert-success-soft mb-2 show',
                        'notice'  => 'alert alert-primary-soft mb-2 show',
                        'warning' => 'alert alert-warning-soft mb-2 show',
                    ]
                );

                return $flash;
            }
        );
    }
}
