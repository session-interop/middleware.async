<?php



// TODO Write service provider


namespace Interop\Session\Middleware\Async;

use Interop\Session\Manager\{SessionManagerInterface, Utils\DefaultManager as DefaultManager};
use Interop\Session\Configuration\SessionConfigurationInterface;

use Interop\Session\SessionInterface;
use Interop\Container\ServiceProvider;

class AsyncSessionMiddlewareServiceProvider implements ServiceProvider {
  
  public function getServices()
    {
        return [
            SessionConfigurationInterface::class => [self::class,'getConfiguration'],
            SessionManagerInterface::class => [self::class,'getManager'],
            AsyncSessionMiddleware::class => [self::class,'getMiddleware']
        ];
    }
    public function getConfiguration($container, $getPrevious = null) {
        if ($getPrevious) {
          return $getPrevious();
        }
        return new \Interop\Session\Manager\Utils\DefaultManager\SessionConfiguration();
    }

    public function getManager($container, $getPrevious = null) {
        if ($getPrevious) {
          return $getPrevious();
        }
        return new DefaultManager($container->get(SessionConfigurationInterface::class));
    }

    public function getMiddleware($container, $getPrevious = null) {
        return new AsyncSessionMiddleware($container->get(SessionManagerInterface::class));
    }
}
