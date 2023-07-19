<?php

namespace zymeli\MqttClient;

use Yii;

/**
 * Class MongodbLogActiveRecord
 * @package common\components\easyMqtt
 *
 * This is the model class for mongodb.Collection "mqtt_log".
 *
 * @property string $_id dbField: varchar
 * @property string $datetime dbField: datetime
 * @property string $source dbField: varchar
 * @property string $topic dbField: varchar
 * @property string $content dbField: varchar
 * @property integer $qos dbField: int
 * @property integer $retain dbField: int
 * @property string $microtime dbField: varchar
 */
class MongodbLogActiveRecord extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_UPDATE, function ($event) {
            $event->isValid = false;
        });
        $this->on(self::EVENT_BEFORE_DELETE, function ($event) {
            $event->isValid = false;
        });
        $this->on(self::EVENT_BEFORE_INSERT, function () {
            $this->getCollection()->createIndex(['microtime' => -1]);
        });
    }

    /**
     * @inheritdoc
     */
    public static function collectionName(): array|string
    {
        return sprintf('mqtt_log_%s', date('Ymd'));
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['datetime', 'source', 'topic', 'content', 'qos', 'retain', 'microtime'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes(): array
    {
        return ['_id', 'datetime', 'source', 'topic', 'content', 'qos', 'retain', 'microtime'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return array_combine($this->attributes(), $this->attributes());
    }
}
