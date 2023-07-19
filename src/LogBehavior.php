<?php

namespace zymeli\MqttClient;

use Yii;
use yii\base\Behavior;

/**
 * Class LogBehavior
 */
class LogBehavior extends Behavior
{
    /**
     * @var EasyMqtt
     * @inheritdoc
     */
    public $owner;

    /**
     * @var bool
     */
    public bool $autoFlush = true;

    /**
     * @inheritdoc
     */
    public function events(): array
    {
        return [
            EasyMqtt::EVENT_BEFORE_PUBLISH => 'beforePublish',
            EasyMqtt::EVENT_AFTER_PUBLISH => 'afterPublish',
        ];
    }

    /**
     * @param PublishEvent $event
     */
    public function beforePublish(PublishEvent $event)
    {
        Yii::info("$event->topic will publish.", EasyMqtt::class);
    }

    /**
     * @param PublishEvent $event
     */
    public function afterPublish(PublishEvent $event)
    {
        Yii::info("$event->topic is published.", EasyMqtt::class);
    }
}
