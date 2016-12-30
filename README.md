# SessionMiddleware


This middleware inject a [`SessionInterface`](https://github.com/session-interop/session-interop) as an [attribute of a psr7's request's](http://www.php-fig.org/psr/psr-7/#3-2-1-psr-http-message-serverrequestinterface). The attribute's name is defined as Introp\\Session\\SessionInterface::class.
It __does not__ reconfigure the session if $\_SESSION exists.
This middleware work in two steps:

1. This middleware open the session (by calling `session_start`) if needed to __*copy*__ it to create a [`ArraySession`](https://github.com/session-interop/session-interop) then close it immediatly (by calling `session_abort`) if it was not started before, to preserve the session state. Once the session copied, it is injected in the PSR7's request and then followings middleware are called.
2. Once every following middleware has been executed, news  or removed session values (as defined [`here`](https://github.com/session-interop/session-interop) is wrote in $\_SESSION and then the session persisted. Once again, it ensure to reopen the session or to let it close depending on the previous session's state.

__Warning:__ Persisted mean there is a manual call to `session_write_close`, that imply all $\_SESSION to be wrote.

## Recommanded

We recommands to place this middleware as soon as possible in the pipe of your application, this way every following middleware will be able to use the request's session.

# Required

This middleware is designed to be used as a [zend-stratigility](https://github.com/zendframework/zend-stratigility) middleware.



## Usage

If you want to change the configuration, you must provide a [`SessionConfigurationInterface`](https://github.com/session-interop/session-configuration-interop) implementation as the factory parameter.
Current server's configuration is used by using the package [`DefaultSessionConfiguration`](https://github.com/session-interop/utils.defaultmanager/blob/master/src/SessionConfiguration.php)

Following example are assuming you use
* [`zend-expressive`](https://github.com/zendframework/zend-expressive) and a container compatible  with
* A [`container-interop`](https://github.com/container-interop/container-interop) as dependencies container



###Basic Usage:


```php
  // Without container

  $mySessionConfiguration = null;
  // If you have custom configuration:
  $mySessionConfiguration = new MySessionConfiguration();

  $sessionMiddleware = \Interop\Session\Middleware\Async\AsyncSessionMiddlewareFactory::createFromConfiguration($mySessionConfiguration);
  // $mySessionConfiguration is optional
  $app->pipe($sessionMiddleware);
  // OR using container
  $factory = new \Interop\Session\Middleware\Async\AsyncSessionMiddlewareFactory();
  $container->set(Interop\Session\Middleware\Async\AsyncSessionMiddlewareServiceProvider::class, $factory->__invoke($container));
  //....
  $app->pipe(
    $container->get(\Interop\Session\Middleware\Async\AsyncSessionMiddlewareServiceProvider::class)
  );
```

Now in every following Middleware:
```php
$session = $request->getAttribute(\Interop\Session\SessionInterface::class);
$session->set("foo", "baz");
//...
echo $session->get("foo"); // print baz

```

### Using service providers:
[`service-provider`](https://github.com/container-interop/service-provider).
This following example use [`Simplex`](https://github.com/mnapoli/simplex)
If the provider find an instance of [`SessionConfigurationInterface`](https://github.com/session-interop/session-configuration-interop) it will be used. To be possible the container __must__ inject the instance inside the container using the name `Interop\Session\Configuration\SessionConfigurationInterface`. This name is got by using:
```php
use Interop\Session\Configuration\SessionConfigurationInterface;
//.....
SessionConfigurationInterface::class
```


```php
$container->register(
  new \Interop\Session\Middleware\Async\AsyncSessionMiddlewareServiceProvider()
);
/// .............
$app->pipe(
  $container->get(\Interop\Session\Middleware\Async\AsyncSessionMiddlewareServiceProvider::class)
)
```

Now in every following Middleware:
```php
$session = $request->getAttribute(\Interop\Session\SessionInterface::class);
$session->set("foo", "baz");
echo $session->get("foo"); // print baz

```

### Using Aura.DI

```php
$factory = "\\Interop\\Session\\Middleware\\Async\\AsyncSessionMiddlewareFactory";
$name = "\\Interop\\Session\\Middleware\\Async\\AsyncSessionMiddleware";
$container->set($factory, $container->lazyNew($factory));
$container->set($name, $container->lazyGetCall($factory, '__invoke', $container));

/// .......

$app->pipe($container->get("\\Interop\\Session\\Middleware\\Async\\AsyncSessionMiddleware"));
```
