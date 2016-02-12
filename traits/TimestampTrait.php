<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\traits;

use yii\behaviors\TimestampBehavior;

/**
 * Entity features concerning timestamp.
 * @property-read array $timestampBehaviors
 * @property-read array $createdAtRules
 * @property-read array $updatedAtRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait TimestampTrait
{

    /**
     * @var string the attribute that will receive datetime value
     * Set this property to false if you do not want to record the creation time.
     */
    public $createdAtAttribute = 'create_time';

    /**
     * @var string the attribute that will receive datetime value.
     * Set this property to false if you do not want to record the update time.
     */
    public $updatedAtAttribute = 'update_time';

    /**
     * @var integer Determine the format of timestamp.
     */
    public $timeFormat = 0;
    public static $timeFormatDatetime = 0;
    public static $timeFormatTimestamp = 1;
    public static $initDatetime = '1970-01-01 00:00:00';
    public static $initTimestamp = 0;

    /**
     * Get the current date & time in format of "Y-m-d H:i:s" or timestamp.
     * You can override this method to customize the return value.
     * @param \yii\base\ModelEvent $event
     * @return string Date & Time.
     * @since 1.1
     */
    public static function getCurrentDatetime($event)
    {
        $sender = $event->sender;
        return $sender->currentDatetime();
    }

    public function currentDatetime()
    {
        if ($this->timeFormat === self::$timeFormatDatetime) {
            return date('Y-m-d H:i:s');
        }
        if ($this->timeFormat === self::$timeFormatTimestamp) {
            return time();
        }
    }

    /**
     * Get init date & time in format of "Y-m-d H:i:s" or timestamp.s
     * @param \yii\base\ModelEvent $event
     * @return string|int
     */
    public static function getInitDatetime($event)
    {
        $sender = $event->sender;
        return $sender->initDatetime();
    }

    public function initDatetime()
    {
        if ($this->timeFormat === self::$timeFormatDatetime) {
            return static::$initDatetime;
        }
        if ($this->timeFormat === self::$timeFormatTimestamp) {
            return static::$initTimestamp;
        }
        return null;
    }

    protected function isInitDatetime($attribute)
    {
        if ($this->timeFormat === self::$timeFormatDatetime) {
            return $this->$attribute == static::$initDatetime || $this->$attribute == null;
        }
        if ($this->timeFormat === self::$timeFormatTimestamp) {
            return $this->$attribute == static::$initTimestamp || $this->$attribute == null;
        }
        return false;
    }

    /**
     * Get the current date & time in format of "Y-m-d H:i:s".
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @return string Date & Time.
     * @since 1.1
     */
    public function onUpdateCurrentDatetime($event)
    {
        return self::getCurrentDatetime($event);
    }

    /**
     * Behaviors associated with timestamp.
     * @return array behaviors
     */
    public function getTimestampBehaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => $this->createdAtAttribute,
                'updatedAtAttribute' => $this->updatedAtAttribute,
                'value' => [$this, 'onUpdateCurrentDatetime'],
            ]
        ];
    }

    /**
     * Get createdAtAttribute.
     * @return string timestamp
     */
    public function getCreatedAt()
    {
        $createdAtAttribute = $this->createdAtAttribute;
        return $this->$createdAtAttribute;
    }

    /**
     * Get rules associated with createdAtAttribute.
     * @return array rules
     */
    public function getCreatedAtRules()
    {
        if (!$this->createdAtAttribute) {
            return [];
        }
        return [
            [[$this->createdAtAttribute], 'safe'],
        ];
    }

    /**
     * Get rules associated with updatedAtAttribute.
     * @return array rules
     */
    public function getUpdatedAtRules()
    {
        if (!$this->updatedAtAttribute) {
            return [];
        }
        return [
            [[$this->updatedAtAttribute], 'safe'],
        ];
    }
}
