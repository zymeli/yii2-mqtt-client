<?php

namespace zymeli\MqttClient;

/**
 * MqttClientInterface
 */
interface MqttClientInterface
{
    public function __construct(array $config = []);

    public function publish(string $topic, string $content, int $qos = 0, bool $retain = false): void;

    public function subscribe(array $topics, int $qos = 0): void;

    public function proc_loop(bool $loop = true): bool|string;
}
