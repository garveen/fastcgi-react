<?php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server(9000, $loop);

$fastcgi = new Garveen\FastCgi\React($socket);

$fastcgi->onRequest = function (Psr\Http\Message\RequestInterface $request) {
    return new Garveen\FastCgi\Response(
        '200',
        ['x-powered-by' => 'garveen/fastcgi-react'],
        'Hello World!'
    );
};

$loop->run();
