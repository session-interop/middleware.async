<?php

namespace Interop\Session\Middleware\Async;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Session\Manager\{
    SessionManagerInterface as SessionManager
};
use Interop\Session\Utils\ArraySession\SavableSession as Session;
use Interop\Session\SessionInterface;

class AsyncSessionMiddleware
{
    protected $factory = null;

    public function __construct(AsyncSessionFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getSession()
    {
        return $this->factory->get();
    }

    public function __invoke(Request $request, Response $response, ?callable $out = null): Response
    {
        $session = $this->getSession();
        $request = $request->withAttribute(SessionInterface::class, $session);
        if ($out) {
            $response = $out($request, $response);
        }
        $this->factory->save();
        return $response;
    }
}
