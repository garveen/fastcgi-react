# Garveen/FastCgi/React

Simply serve `fastcgi` with [reactphp](https://github.com/reactphp/socket)

```php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server(9000, $loop);

$fastcgi = new Garveen\FastCgi\React($socket);

// see http://www.php-fig.org/psr/psr-7/
$fastcgi->onRequest = function (Psr\Http\Message\RequestInterface $request) {
    return new Garveen\FastCgi\Response(
        '200', // status code
        ['x-powered-by' => 'garveen/fastcgi-react'], // headers
        'Hello World!' // body
    );
};

$loop->run();
```