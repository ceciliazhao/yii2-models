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
 * 
 * @property-read array $timestampBehaviors
 * @property-read array createdAtRules
 * @property-read array updatedAtRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait TimestampTrait {

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
    public $timeFormat = 0;
    public static $TIME_FORMAT_DATE_TIME = 0;
    public static $TIME_FORMAT_TIMESTAMP = 1;

    /**
     * Get the current date & time in format of "Y-m-d H:i:s".
     * You can override this method to customize the return value.
     * @return string Date & Time.
     * @since 1.1
     */
    public static function getCurrentDatetime($event) {
        $sender = $event->sender;
        if ($sender->timeFormat === self::$TIME_FORMAT_DATE_TIME) {
            return date('Y-m-d H:i:s');
        }
        if ($sender->timeFormat === self::$TIME_FORMAT_TIMESTAMP) {
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
    public function onUpdateCurrentDatetime($event) {
        return self::getCurrentDatetime($event);
    }

    public function getTimestampBehaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => $this->createdAtAttribute,
                'updatedAtAttribute' => $this->updatedAtAttribute,
                'value' => [$this, 'onUpdateCurrentDatetime'],
            ]
        ];
    }

    public function getCreatedAtRules() {
        if (!$this->createdAtAttribute) return [];
        return [
            [[$this->createdAtAttribute], 'safe'],
        ];
    }

    public function getUpdatedAtRules() {
        if (!$this->updatedAtAttribute) return [];
        return [
            [[$this->updatedAtAttribute], 'safe'],
        ];
    }

}
