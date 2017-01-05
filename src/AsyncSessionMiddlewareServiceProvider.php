<?php

namespace Interop\Session\Middleware\Async;

use Interop\Container\ContainerInterface as Container;
use Interop\Session\Manager\SessionManagerInterface;
use Interop\Container\ServiceProvider;

class AsyncSessionMiddlewareServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
          AsyncSessionFactory::class => function (Container $container) {
              return new AsyncSessionFactory($container->get(SessionManagerInterface::class));
          },
            AsyncSessionMiddleware::class => function (Container $container) {
                return new AsyncSessionMiddleware($container->get(AsyncSessionFactory::class));
            },
        ];
    }
}
