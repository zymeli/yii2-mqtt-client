<?php

namespace zymeli\MqttClient;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class EasyMqtt
 *
 * 基于github上的php类MQTT.client的Yii组件包装
 * 组件配置参考:
 *        'easyMqtt' => [
 *            'class' => 'zymeli\MqttClient\EasyMqtt',
 *            'client' => 'zymeli\MqttClient\phpMqttClient',
 *            'config' => [
 *                'server' => '127.0.0.1',
 *                'port' => '1883',
 *                'username' => null,
 *                'password' => null,
 *                'cafile' => null,
 *            ],
 *        ],
 *
 */
class EasyMqtt extends \yii\base\Component
{
    /**
     * @event PublishEvent
     */
    const EVENT_BEFORE_PUBLISH = 'beforePublish';

    /**
     * @event PublishEvent
     */
    const EVENT_AFTER_PUBLISH = 'afterPublish';

    /** @var array EasyMqtt.config */
    public array $config = [];

    /** @var string|MqttClientInterface */
    public string|MqttClientInterface $client;

    /**
     * {@inheritdoc}
     * @param array $config EasyMqtt.config
     * @throws \Exception
     */
    public function init(array $config = [])
    {
        parent::init();
        $this->mergeConfig($config);
        $this->config['clientid'] = 'easy_mqtt_' . uniqid();

        // client
        $client = ($this->client ?? null);
        if (!is_subclass_of($client, MqttClientInterface::class)) {
            throw new InvalidConfigException('Invalid Configuration (Invalid MQTT Client).');
        }
        $this->client = (is_object($client) ? $client : new $client($this->config));
    }

    /**
     * 整理合并配置
     * @param array $config EasyMqtt.config
     * @return void
     */
    protected function mergeConfig(array $config): void
    {
        // for callable
        $fn = function (&$v) use (&$fn) {
            if (is_array($v)) array_walk($v, $fn);
            else $v = (is_callable($v) ? call_user_func($v) : $v);
        };
        // config
        $config = (array)(is_callable($config) ? call_user_func($config) : $config);
        array_walk($config, $fn);
        // default
        $def_conf = [];
        // system
        $sys_conf = $this->config;
        $sys_conf = (array)(is_callable($sys_conf) ? call_user_func($sys_conf) : $sys_conf);
        array_walk($sys_conf, $fn);
        // all
        $this->config = ArrayHelper::merge($def_conf, $sys_conf, $config);
    }

    /**
     * 发布主题
     * @param string $topic 主题
     * @param string $content 内容
     * @param int $qos 服务质量
     * @param bool $retain 是否保留
     */
    public function publish(string $topic, string $content, int $qos = 0, bool $retain = false): void
    {
        // event
        $event = new PublishEvent([
            'topic' => $topic,
            'content' => $content,
            'qos' => $qos,
            'retain' => $retain,
        ]);
        // event.before
        $this->trigger(self::EVENT_BEFORE_PUBLISH, $event);
        if ($event->handled) return;
        // publish
        $this->client->publish($topic, $content, $qos, $retain);
        // event.after
        $this->trigger(self::EVENT_AFTER_PUBLISH, $event);
    }

    /**
     * 订阅主题
     * @param array $topics 主题数组:[topic=>[qos=>0,function=>fn],...]
     * @param int $qos 服务质量
     */
    public function subscribe(array $topics, int $qos = 0): void
    {
        $this->client->subscribe($topics, $qos);
    }

    /**
     * 进程循环返回结果信息
     * @param bool $loop 是否继续循环
     * @return bool|string
     */
    public function proc_loop(bool $loop = true): bool|string
    {
        return $this->client->proc_loop($loop);
    }
}
