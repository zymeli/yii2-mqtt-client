<?php

namespace zymeli\MqttClient;

use Yii;
use yii\base\Event;

/**
 * Class PublishEvent
 */
class PublishEvent extends Event
{
    /**
     * @var EasyMqtt
     * @inheritdoc
     */
    public $sender;

    /**
     * @var string|null
     */
    public ?string $id;

    /**
     * @var string
     */
    public string $topic;

    /**
     * @var string
     */
    public string $content;

    /**
     * @var int qos:0/1/2
     */
    public int $qos;

    /**
     * @var bool
     */
    public bool $retain;
}
