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

/**
 * Description of IdentityTrait
 *
 * @property-read int $id
 * @property-read string $authKey
 * @property-read string $guid
 * @property array $statusRules
 * @property array $authKeyRules
 * @property array $accessTokenRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait IdentityTrait {

    public static $STATUS_ACTIVE = 1;
    public static $STATUS_INACTIVE = 0;
    public $statusAttribute = 'status';
    private $_statusRules = [];
    public $authKeyAttribute = 'auth_key';
    private $_authKeyRules = [];
    public $accessTokenAttribute = 'access_token';
    private $_accessTokenRules = [];

    public static function findIdentity($id) {
        $self = (self::className());
        return static::findOne([(new $self(['skipInit' => true]))->idAttribute => $id]);
    }

    public static function findIdentityByGuid($guid) {
        return static::findOne($guid);
    }

    public static function findIdentityByAccessToken($token, $type = NULL) {
        return static::findOne(['access_token' => $token]);
    }

    public function getId() {
        $idAttribute = $this->idAttribute;
        if (empty($idAttribute))
            return false;
        return $this->$idAttribute;
    }

    public function getGuid() {
        $guidAttribute = $this->guidAttribute;
        return $this->$guidAttribute;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    public function getAuthKeyRules() {
        if (empty($this->_authKeyRules)) {
            $this->_authKeyRules = [
                [[$this->authKeyAttribute], 'required'],
                [[$this->authKeyAttribute], 'string', 'max' => 32],
            ];
        }
        return $this->_authKeyRules;
    }

    public function setAuthKeyRules($rules) {
        if (!empty($rules) && is_array($rules)) {
            $this->_authKeyRules = $rules;
        }
    }

    public function getAccessTokenRules() {
        if (empty($this->_accessTokenRules)) {
            $this->_accessTokenRules = [
                [[$this->accessTokenAttribute], 'required'],
                [[$this->accessTokenAttribute], 'string', 'max' => 32],
            ];
        }
        return $this->_accessTokenRules;
    }

    public function setAccessTokenRules($rules) {
        if (!empty($rules) && is_array($rules)) {
            $this->_accessTokenRules = $rules;
        }
    }

    /**
     * 
     * @return type
     */
    public function getStatusRules() {
        if (empty($this->_statusRules)) {
            $this->_statusRules = [
                [[$this->statusAttribute], 'required'],
                [[$this->statusAttribute], 'integer', 'min' => 0],
            ];
        }
        return $this->_statusRules;
    }

    /**
     * 
     * @param type $rules
     */
    public function setStatusRules($rules) {
        if (!empty($rules) && is_array($rules)) {
            $this->_statusRules = $rules;
        }
    }

    /**
     * 
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onInitStatusAttribute($event) {
        $sender = $event->sender;
        $statusAttribute = $sender->statusAttribute;
        $sender->$statusAttribute = self::$STATUS_ACTIVE;
    }

}
