<?php

namespace zymeli\MqttClient;

use Bluerhinos\phpMQTT;

/**
 * Class phpMqttClient
 */
class phpMqttClient implements MqttClientInterface
{
    private phpMQTT $client;

    public function __construct(array $config = [])
    {
        $server = $config['server'] ?: "127.0.0.1";
        $port = $config['port'] ?: "1883";
        $clientid = $config['clientid'] ?: uniqid();
        $cafile = $config['cafile'] ?: null;
        $this->client = new phpMQTT($server, $port, $clientid, $cafile);
        $username = $config['username'];
        $password = $config['password'];
        $this->client->connect(true, null, $username, $password);
    }

    public function publish(string $topic, string $content, int $qos = 0, bool $retain = false): void
    {
        $this->client->publish($topic, $content, $qos, $retain);
    }

    public function subscribe(array $topics, int $qos = 0): void
    {
        // 不要重复订阅主题
        static $old_topic_keys = [];
        foreach ($topics as $key => $arr) {
            if (in_array($key, $old_topic_keys)) {
                $this->client->topics[$key]['function'] = $arr['function'];
                unset($topics[$key]);
            } else {
                $old_topic_keys[] = $key;
            }
        }
        $topics and $this->client->subscribe($topics, $qos);
    }

    public function proc_loop(bool $loop = true): bool|string
    {
        return $this->client->proc($loop);
    }
}
