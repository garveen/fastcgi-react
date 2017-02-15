<?php

namespace Garveen\FastCgi;

use React\Socket\ServerInterface as SocketServerInterface;
use React\Socket\ConnectionInterface;
use Psr\Http\Message\ServerRequestInterface;

class React
{

    protected $socket;
    protected $fastcgi;
    protected $connections = [];
    protected $fd = 0;

    public $onRequest;

    public function __construct(SocketServerInterface $socket = null)
    {
        $this->socket = $socket;
        $that = $this;

        $this->fastcgi = new FastCgi(
            [$this, 'requestCallback'],
            [$this, 'sendCallback'],
            [$this, 'closeCallback']
        );

        $this->socket->on('connection', function (ConnectionInterface $conn) use ($that) {
            $that->fd++;
            $fd = $that->fd;
            $that->connections[$fd] = $conn;

            $conn->on('data', function ($data) use ($fd, $that) {
                $that->fastcgi->receive($fd, $data);
            });

            $conn->on('close', function () use ($fd, $that) {
                unset($that->connections[$fd]);
            });
        });
    }

    public function requestCallback(ServerRequestInterface $serverRequest)
    {
        $onRequest = $this->onRequest;
        return $onRequest($serverRequest);
    }

    public function sendCallback($fd, $data)
    {
        $this->connections[$fd]->write($data);
    }

    public function closeCallback($fd)
    {
        $this->connections[$fd]->end();
    }
}
