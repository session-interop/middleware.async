<?php

namespace Interop\Session\Middleware\Async;

use Psr\Http\Message\{
    ServerRequestInterface as Request, ResponseInterface as Response
};
use Interop\Session\Manager\{
    SessionManagerInterface as SessionManager
};
use Interop\Session\Utils\ArraySession\SavableSession as Session;
use Zend\Stratigility\MiddlewareInterface;
use Interop\Session\SessionInterface;

class AsyncSessionMiddleware implements MiddlewareInterface
{

    protected $manager = null;

    public function __construct(SessionManager $manager)
    {
        $this->sessionManager = $manager;
    }

    public function __invoke(Request $request, Response $response, ?callable $out = NULL)
    {
        $oldActive = $this->sessionManager->isSessionActive();
        $this->sessionManager->ensureSessionHasStart();
        $currentSession = $_SESSION;
        $session = new Session($currentSession, "PHPCAN");
        if (!$oldActive) {
            $this->sessionManager->close();
        }
        $request = $request->withAttribute(SessionInterface::class, $session);
        if ($out) {
            $response = $out($request, $response);
        }
        $oldActive = $this->sessionManager->isSessionActive();
        $this->sessionManager->ensureSessionHasStart();
        foreach ($session as $key => $value) {
            if (!array_key_exists($key, $currentSession) || $currentSession[$key] != $value) {
                $_SESSION[$key] = $value;
            }
        }
        $this->sessionManager->ensureCommit();
        if ($oldActive) {
            $this->sessionManager->ensureSessionHasStart();
        }
        return $response;
    }
}
