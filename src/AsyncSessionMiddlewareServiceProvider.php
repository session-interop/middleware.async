<?php

namespace Interop\Session\Middleware\Async;

use Interop\Container\ContainerInterface as Container;
use Interop\Session\Manager\SessionManagerInterface;
use Interop\Container\ServiceProvider;

use TheCodingMachine\MiddlewareListServiceProvider;
use TheCodingMachine\MiddlewareOrder;

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
            MiddlewareListServiceProvider::MIDDLEWARES_QUEUE => [self::class, 'updatePriorityQueue'],
        ];
    } 
    public static function updatePriorityQueue(Container $container, callable $previous = null) : \SplPriorityQueue
    {
        if ($previous) {
            $priorityQueue = $previous();
            $priorityQueue->insert($container->get(AsyncSessionMiddleware::class), MiddlewareOrder::UTILITY_EARLY);
            return $priorityQueue;
        } else {
            throw new \InvalidArgumentException("Could not find declaration for service '".MiddlewareListServiceProvider::MIDDLEWARES_QUEUE."'.");
        }
    }

}
 