<?php

namespace Yonna\Console;

use Exception;
use swoole_server;
use Yonna\Bootstrap\BootType;
use Yonna\Core;
use Yonna\IO\RequestBuilder;
use Yonna\Response\Collector;

/**
 * Class SwooleTcp
 * @package Yonna\Console
 */
class SwooleTcp extends Console
{

    private $server = null;
    private string $root_path;
    private array $options;

    /**
     * SwooleHttp constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct(string $root_path, array $options)
    {
        if (!class_exists('swoole_server')) {
            throw new Exception('class swoole_server not exists');
        }
        $this->root_path = $root_path;
        $this->options = $options;
        $this->checkParams($this->options, ['p', 'e']);
        return $this;
    }


    /**
     * build a request
     * @param mixed ...$options
     * @return RequestBuilder
     */
    private function requestBuilder(...$options): RequestBuilder
    {
        $server = $options[0];
        $task_id = $options[1];
        $from_id = $options[2];
        $request = $options[3];
        $client_id = BootType::SWOOLE_TCP . '#' . $server->worker_id;

        /**
         * @var RequestBuilder $requestBuilder
         */
        $requestBuilder = Core::get(RequestBuilder::class);
        $requestBuilder->setSwoole($server);
        $requestBuilder->setRawData($request['rawData'] ?? '');
        $requestBuilder->setRequestMethod('STREAM');
        $requestBuilder->setContentLength(strlen($requestBuilder->getRawData()));
        $requestBuilder->setContentType('application/json');
        $requestBuilder->setClientId($client_id);
        return $requestBuilder;
    }

    /**
     * run
     */
    public function run()
    {
        $this->server = new swoole_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
            'heartbeat_check_interval' => 30,
            'heartbeat_idle_time' => 600,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on('connect', function ($server, $fd) {
            echo "connection open: {$fd}\n";
        });
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $request = [];
            $request['rawData'] = $data;
            $this->server->task($request, -1, function ($server, $task_id, Collector $responseCollector) use ($fd) {
                if ($responseCollector !== false) {
                    $server->send($fd, $responseCollector->response());
                }
            });
        });
        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $ResponseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::SWOOLE_TCP,
                $this->requestBuilder($server, $task_id, $from_id, $request)
            );
            $this->server->finish($ResponseCollector);
        });

        $this->server->on('finish', function ($server, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->start();
    }
}
