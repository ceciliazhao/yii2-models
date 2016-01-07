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

use Yii;

/**
 * 
 * @property-read boolean $isConfirmed
 * @property integer $confirmation
 * @property-read array $confirmationRules
 * @property string $confirmCode
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait ConfirmationTrait {

    /**
     * @var int 
     */
    public static $confirmFalse = 0;
    public static $confirmTrue = 1;

    /**
     * @var string|boolean 
     */
    public $confirmationAttribute = false;

    /**
     * @var string if $confirmationAttribute is not empty or false, this
     * attribute must be specified, otherwith exception would be thrown.
     */
    public $confirmCodeAttribute = 'confirm_code';

    /**
     * @var integer The expiration in second.
     */
    public $confirmCodeExpiration = 3600;

    /**
     * @var string 
     */
    public $confirmTimeAttribute = 'confirm_time';

    /**
     * @var string 
     */
    public $initConfirmTime = '1970-01-01 00:00:00';
    public static $eventConfirmationChanged = "confirmationChanged";
    public static $eventConfirmationCanceled = "confirmationCanceled";
    public static $eventConfirmationSuceeded = "confirmationSucceeded";

    /**
     * 
     * @return boolean
     * @throws \yii\base\NotSupportedException
     */
    public function applyConfirmation() {
        if (!$this->confirmCodeAttribute) {
            throw new \yii\base\NotSupportedException('This method is not implemented.');
        }
        $this->confirmCode = $this->generateConfirmationCode();
        if (!$this->save()) {
            return false;
        }
    }

    /**
     * 
     * @param string $code
     */
    public function setConfirmCode($code) {
        $confirmCodeAttribute = $this->confirmCodeAttribute;
        $this->$confirmCodeAttribute = $code;
        $confirmTimeAttribute = $this->confirmTimeAttribute;
        if (!empty($code)) {
            $this->$confirmTimeAttribute = date('Y-m-d H:i:s');
        } else {
            $this->$confirmTimeAttribute = $this->initConfirmTime;
        }
    }

    /**
     * 
     * @return string
     */
    public function getConfirmCode() {
        $confirmCodeAttribute = $this->confirmCodeAttribute;
        if (!$confirmCodeAttribute) {
            return null;
        }
        return $this->$confirmCodeAttribute;
    }

    /**
     * 
     * @param string $code
     * @return boolean
     */
    public function Confirm($code) {
        if (!$this->validateConfirmationCode($code)) {
            return false;
        }
        $this->confirmation = self::$confirmTrue;
        return $this->save();
    }

    /**
     * 
     * @return type
     */
    public function generateConfirmationCode() {
        return substr(sha1(Yii::$app->security->generateRandomString()), 0, 8);
    }

    /**
     * 
     * @param type $code
     * @return type
     */
    public function validateConfirmationCode($code) {
        $confirmCodeAttribute = $this->confirmCodeAttribute;
        return $this->$confirmCodeAttribute === $code;
    }

    /**
     * Get confirmation status of current model.
     * @return boolean Whether current model has been confirmed.
     */
    public function getIsConfirmed() {
        $confirmationAttribute = $this->confirmationAttribute;
        return $this->$confirmationAttribute > self::$confirmFalse;
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
        $sender->confirmation = self::$confirmFalse;
        $sender->confirmCode = '';
    }

    /**
     * 
     * @param type $value
     */
    public function setConfirmation($value) {
        $confirmationAttribute = $this->confirmationAttribute;
        $this->$confirmationAttribute = $value;
        $this->trigger(self::$eventConfirmationChanged);
    }
    
    public function getConfirmation() {
        $confirmationAttribute = $this->confirmationAttribute;
        return $this->$confirmationAttribute;
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
        $confirmationAttribute = $sender->confirmationAttribute;
        if ($sender->isAttributeChanged($confirmationAttribute)) {
            $sender->confirmCode = '';
            if ($sender->$confirmationAttribute == self::$confirmFalse) {
                $sender->trigger(self::$eventConfirmationCanceled);
            } else {
                $sender->trigger(self::$eventConfirmationSuceeded);
                $sender->resetOthersConfirmation();
            }
        }
    }

    /**
     * 
     * @return array
     */
    public function getConfirmationRules() {
        if ($this->confirmationAttribute) {
            return [
                [[$this->confirmationAttribute], 'integer', 'min' => 0],
                [[$this->confirmTimeAttribute], 'safe'],
            ];
        } else {
            return [];
        }
    }

    /**
     * When the content changed, reset confirmation status.
     */
    protected function resetConfirmation() {
        $contentAttribute = $this->contentAttribute;
        if (is_array($contentAttribute)) {
            foreach ($contentAttribute as $attribute) {
                if ($this->isAttributeChanged($attribute)) {
                    $this->confirmation = self::$confirmFalse;
                    break;
                }
            }
        } elseif ($this->isAttributeChanged($contentAttribute)) {
            $this->confirmation = self::$confirmFalse;
        }
    }
    
    /**
     * 
     */
    protected function resetOthersConfirmation() {
        $contents = self::find()->where([$this->contentAttribute => $this->content])->andWhere(['not', $this->createdByAttribute, $this->creator])->all();
        foreach ($contents as $content) {
            $content->confirmation = self::$confirmFalse;
            $content->save();
        }
    }

}
