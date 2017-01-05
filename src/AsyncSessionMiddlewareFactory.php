<?php

namespace Interop\Session\Middleware\Async;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Session\Manager\SessionManagerInterface as SessionManager;
use Interop\Session\Manager\Utils\DefaultManager\DefaultSessionManager as DefaultManager;
use Interop\Session\Configuration\SessionConfigurationInterface;

use Interop\Session\SessionInterface;

class AsyncSessionMiddlewareFactory
{
    public function __invoke(\Interop\Container\ContainerInterface $container)
    {
        $manager = $container->get(SessionManager::class);
        if ($container->has(SessionManager::class)) {
            return new AsyncSessionMiddleware($container->get(SessionManager::class));
        }

        $config = null;
        if ($container->has(SessionConfigurationInterface::class)) {
            $config = $container->get(SessionConfigurationInterface::class);
        }
        return self::create($config);
    }

    public static function create(?SessionConfigurationInterface $configuration = null)
    {
        if (!$configuration) {
            $configuration = new \Interop\Session\Manager\Utils\DefaultManager\SessionConfiguration();
        }
        $manager = new DefaultManager($configuration);
        return new AsyncSessionMiddleware($manager);
    }
}
