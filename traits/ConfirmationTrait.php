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
 * This trait allow its owner to enable the entity to be blamed by user.
 * @property-read boolean $isConfirmed
 * @property integer $confirmation
 * @property-read array $confirmationRules
 * @property string $confirmCode the confirm code used for confirming the content. 
 * You can disable this attribute and create a new model for storing confirm code as
 * its low-frequency usage.
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait ConfirmationTrait {

    /**
     * @var int 
     */
    public static $confirmFalse = 0;
    
    /**
     * @var int 
     */
    public static $confirmTrue = 1;

    /**
     * @var string|boolean 
     */
    public $confirmationAttribute = false;

    /**
     * @var string This attribute specify the name of confirm_code attribute, if
     * this attribute is assigned to false, this feature will be ignored.
     * if $confirmationAttribute is empty or false, this attribute will be skipped.
     */
    public $confirmCodeAttribute = 'confirm_code';

    /**
     * @var integer The expiration in seconds. If $confirmCodeAttribute is
     * specified, this attribute must be specified.
     */
    public $confirmCodeExpiration = 3600;

    /**
     * @var string This attribute specify the name of confirm_time attribute. if
     * this attribute is assigned to false, this feature will be ignored.
     * if $confirmationAttribute is empty or false, this attribute will be skipped.
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
        if (!$this->confirmCodeAttribute) {
            return;
        }
        $confirmCodeAttribute = $this->confirmCodeAttribute;
        $this->$confirmCodeAttribute = $code;
        if (!$this->confirmTimeAttribute) {
            return;
        }
        $confirmTimeAttribute = $this->confirmTimeAttribute;
        if (!empty($code)) {
            $this->$confirmTimeAttribute = date('Y-m-d H:i:s');
            return;
        }
        $this->$confirmTimeAttribute = $this->initConfirmTime;
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
     * Confirm the current content.
     * @param string $code
     * @return boolean
     */
    public function confirm($code) {
        if (!$this->confirmationAttribute || !$this->validateConfirmationCode($code)) {
            return false;
        }
        $this->confirmation = self::$confirmTrue;
        return $this->save();
    }

    /**
     * 
     * @return string
     */
    public function generateConfirmationCode() {
        return substr(sha1(Yii::$app->security->generateRandomString()), 0, 8);
    }

    /**
     * Validate the confirmation code.
     * @param string $code
     * @return boolean Whether the confirmation code is valid.
     */
    public function validateConfirmationCode($code) {
        $confirmCodeAttribute = $this->confirmCodeAttribute;
        if (!$confirmCodeAttribute)
            return true;
        return $this->$confirmCodeAttribute === $code;
    }

    /**
     * Get confirmation status of current model.
     * @return boolean Whether current model has been confirmed.
     */
    public function getIsConfirmed() {
        $confirmationAttribute = $this->confirmationAttribute;
        if (!$confirmationAttribute)
            return true;
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
     * @param mixed $value
     */
    public function setConfirmation($value) {
        $confirmationAttribute = $this->confirmationAttribute;
        if (!$confirmationAttribute)
            return;
        $this->$confirmationAttribute = $value;
        $this->trigger(self::$eventConfirmationChanged);
    }

    /**
     * 
     * @return mixed
     */
    public function getConfirmation() {
        $confirmationAttribute = $this->confirmationAttribute;
        if (!$confirmationAttribute)
            return null;
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
        if (!$confirmationAttribute)
            return;
        if ($sender->isAttributeChanged($confirmationAttribute)) {
            $sender->confirmCode = '';
            if ($sender->$confirmationAttribute == self::$confirmFalse) {
                $sender->trigger(self::$eventConfirmationCanceled);
                return;
            }
            $sender->trigger(self::$eventConfirmationSuceeded);
            $sender->resetOthersConfirmation();
        }
    }

    /**
     * 
     * @return array
     */
    public function getConfirmationRules() {
        if (!$this->confirmationAttribute) {
            return [];
        }
        return [
            [[$this->confirmationAttribute], 'integer', 'min' => 0],
            [[$this->confirmTimeAttribute], 'safe'],
        ];
    }

    /**
     * When the content changed, reset confirmation status.
     */
    protected function resetConfirmation() {
        $contentAttribute = $this->contentAttribute;
        if (!$contentAttribute)
            return;
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
     * Reset others' confirmation when the others own the same content.
     */
    protected function resetOthersConfirmation() {
        if (!$this->confirmationAttribute || empty($this->userClass))
            return;
        $contents = self::find()->where([$this->contentAttribute => $this->content])->andWhere(['not', $this->createdByAttribute, $this->creator])->all();
        foreach ($contents as $content) {
            $content->confirmation = self::$confirmFalse;
            $content->save();
        }
    }

}
