<?php

namespace Interop\Session\Middleware\Async;

use Interop\Container\ContainerInterface;
use Interop\Session\Manager\SessionManagerInterface;
use Interop\Container\ServiceProvider;

class AsyncSessionMiddlewareServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            AsyncSessionMiddleware::class => function (ContainerInterface $container) {
                return new AsyncSessionMiddleware($container->get(SessionManagerInterface::class));
            },
        ];
    }
}
