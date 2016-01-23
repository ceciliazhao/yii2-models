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

    /**
     * Get the current date & time in format of "Y-m-d H:i:s".
     * You can override this method to customize the return value.
     * @return string Date & Time.
     * @since 1.1
     */
    public static function getCurrentDatetime($event)
    {
        $sender = $event->sender;
        if ($sender->timeFormat === self::$timeFormatDatetime) {
            return date('Y-m-d H:i:s');
        }
        if ($sender->timeFormat === self::$timeFormatTimestamp) {
            return time();
        }
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
     * 
     * @return array
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
     * 
     * @return string
     */
    public function getCreatedAt()
    {
        $createdAtAttribute = $this->createdAtAttribute;
        return $this->$createdAtAttribute;
    }

    /**
     * 
     * @return array
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
     * 
     * @return array
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
