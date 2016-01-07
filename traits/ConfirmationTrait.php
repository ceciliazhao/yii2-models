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

/**
 * 
 * @property-read boolean $isConfirmed
 * @property-write integer $confirmation
 * @property-read array $confirmationRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait ConfirmationTrait {

    /**
     *
     * @var type 
     */
    public static $CONFIRM_FALSE = 0;

    /**
     *
     * @var string|boolean 
     */
    public $confirmationAttribute = false;

    /**
     *
     * @var string 
     */
    public $confirmTimeAttribute = 'confirm_time';

    /**
     *
     * @var string 
     */
    public $initConfirmTime = '1970-01-01 00:00:00';
    public static $EVENT_CONFIRMATION_CHANGED = "confirmationChanged";
    public static $EVENT_CONFIRMATION_CANCELED = "confirmationCanceled";
    public static $EVENT_CONFIRMATION_SUCCEEDED = "confirmationSucceeded";

    /**
     * Get confirmation status of current model.
     * @return boolean Whether current model has been confirmed.
     */
    public function getIsConfirmed() {
        $confirmationAttribute = $this->confirmationAttribute;
        return $this->$confirmationAttribute > self::$CONFIRM_FALSE;
    }

    /**
     * Initialize the confirmation status.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onInitConfirmation($event) {
        $sender = $event->sender;
        if (!$sender->confirmationAttribute) {
            return;
        }
        $confirmationAttribute = $sender->confirmationAttribute;
        $confirmTimeAttribute = $sender->confirmTimeAttribute;
        $sender->$confirmationAttribute = self::$CONFIRM_FALSE;
        $sender->$confirmTimeAttribute = $sender->initConfirmTime;
    }

    /**
     * 
     * @param type $value
     */
    public function setConfirmation($value) {
        $confirmationAttribute = $this->confirmationAttribute;
        $this->$confirmationAttribute = $value;
        $this->trigger(self::$EVENT_CONFIRMATION_CHANGED);
    }

    /**
     * When confirmation status changed, this event will be triggered. If
     * confirmation succeeded, the confirm_time will be assigned to current time,
     * or the confirm_time will be assigned to initConfirmTime.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onConfirmationChanged($event) {
        $sender = $event->sender;
        $confirmationAttribute = $this->confirmationAttribute;
        if ($sender->isAttributeChanged($confirmationAttribute)) {
            $confirmTimeAttribute = $this->confirmTimeAttribute;
            if ($sender->$confirmationAttribute == self::$CONFIRM_FALSE) {
                $sender->$confirmTimeAttribute = $sender->initConfirmTime;
                $sender->trigger(self::$EVENT_CONFIRMATION_CANCELED);
            } else {
                $sender->$confirmTimeAttribute = $sender->currentDatetime;
                $sender->trigger(self::$EVENT_CONFIRMATION_SUCCEEDED);
            }
        }
    }

    public function getConfirmationRules() {
        if ($this->confirmationAttribute)
        {
            return [
                [[$this->confirmationAttribute], 'integer', 'min' => 0],
                [[$this->confirmTimeAttribute], 'safe'],
            ];
        } else {
            return [];
        }
    }

    /**
     * When the content changes, reset confirmation status.
     */
    protected function resetConfirmation() {
        $contentAttribute = $this->contentAttribute;
        if (is_array($contentAttribute)) {
            foreach ($contentAttribute as $attribute) {
                if ($this->isAttributeChanged($attribute)) {
                    $this->confirmation = self::$CONFIRM_FALSE;
                    break;
                }
            }
        } else {
            if ($this->isAttributeChanged($contentAttribute))
                $this->confirmation = self::$CONFIRM_FALSE;
        }
    }

}
