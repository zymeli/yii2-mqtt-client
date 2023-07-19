<?php

namespace zymeli\MqttClient;

use Yii;

/**
 * MongodbLogBehavior
 */
class MongodbLogBehavior extends LogBehavior
{
    /**
     * @param PublishEvent $event
     */
    public function afterPublish(PublishEvent $event)
    {
        Yii::info("$event->topic is published.", EasyMqtt::class);
        // 记录mqtt日志
        $mqttLog = new MongodbLogActiveRecord();
        $mqttLog->datetime = date("Y-m-d H:i:s");
        $mqttLog->source = 'afterPublish';
        $mqttLog->topic = $event->topic;
        $mqttLog->content = $event->content;
        $mqttLog->qos = $event->qos;
        $mqttLog->retain = $event->retain;
        $mqttLog->microtime = preg_replace('/^0(.\d+) (\d+)$/', '$2$1', microtime());
        $mqttLog->save();
    }
}
