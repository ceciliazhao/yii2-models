<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\traits;

use Yii;

/**
 * Description of RegistrationTrait
 *
 * @property array $sourceRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait RegistrationTrait {

    public static $EVENT_AFTER_REGISTER = "afterRegister";
    public static $EVENT_BEFORE_REGISTER = "beforeRegister";
    public static $EVENT_REGISTER_FAILED = "registerFailed";
    public static $EVENT_AFTER_DEREGISTER = "afterDeregister";
    public static $EVENT_BEFORE_DEREGISTER = "beforeDeregister";
    public static $EVENT_DEREGISTER_FAILED = "deregisterFailed";
    public $sourceAttribute = 'source';
    private $_sourceRules = [];
    public $sourceSelf = '0';

    /**
     * Register new user.
     * It is equivalent to store the current user and its associated models into
     * database synchronously. The registration will be terminated immediately
     * if any errors occur in the process, and all the earlier steps succeeded
     * are rolled back.
     * If current user is not a new one(isNewRecord = false), the registration
     * will be skipped and return false.
     * The $EVENT_BEFORE_REGISTER will be triggered before registration starts.
     * If registration finished, the $EVENT_AFTER_REGISTER will be triggered. or
     * $EVENT_REGISTER_FAILED will be triggered when any errors occured.
     * @param array $associatedModels The models associated with user to be stored synchronously.
     * @return boolean Whether the registration succeeds or not.
     * @throws \yii\db\IntegrityException
     */
    public function register($associatedModels = []) {
        if (!$this->isNewRecord) {
            return false;
        }
        $this->trigger(self::$EVENT_BEFORE_REGISTER);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->save()) {
                throw new \yii\db\IntegrityException('Registration Error(s) Occured.', $this->errors);
            }
            foreach ($associatedModels as $model) {
                if (!$model->save()) {
                    throw new \yii\db\IntegrityException('Registration Error(s) Occured.', $model->errors);
                }
            }
            $transaction->commit();
        } catch (\yii\db\Exception $ex) {
            $transaction->rollBack();
            Yii::warning($ex->errorInfo, 'user\register');
            $this->trigger(self::$EVENT_REGISTER_FAILED);
            return false;
        }
        $this->trigger(self::$EVENT_AFTER_REGISTER);
        return true;
    }

    /**
     * Deregister current uset itself.
     * It is equivalent to delete current user and its associated models. BUT it
     * deletes current user ONLY, the associated models will not be deleted
     * forwardly. So you should set the foreign key of associated models' table
     * referenced from primary key of user table, and their association mode is
     * 'on update cascade' and 'on delete cascade'.
     * @return boolean Whether deregistration succeeds or not.
     */
    public function deregister() {
        $this->trigger(self::$EVENT_BEFORE_DEREGISTER);
        $result = $this->delete();
        if ($result) {
            $this->trigger(self::$EVENT_AFTER_DEREGISTER);
        } else {
            $this->trigger(self::$EVENT_DEREGISTER_FAILED);
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getSourceRules() {
        if (empty($this->_sourceRules)) {
            $this->_sourceRules = [
                [[$this->sourceAttribute], 'required'],
                [[$this->sourceAttribute], 'string'],
            ];
        }
        return $this->_sourceRules;
    }

    /**
     * 
     * @param array $rules
     */
    public function setSourceRules($rules) {
        if (!empty($rules) && is_array($rules)) {
            $this->_sourceRules = $rules;
        }
    }

    /**
     * Initialize the source attribute with $sourceSelf.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onInitSourceAttribute($event) {
        $sender = $event->sender;
        $sourceAttribute = $sender->sourceAttribute;
        $sender->$sourceAttribute = $sender->sourceSelf;
    }

}
