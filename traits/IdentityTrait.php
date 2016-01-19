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
 * User features concerning identity.
 *
 * @property-read string $authKey
 * @property array $statusRules
 * @property array $authKeyRules
 * @property array $accessTokenRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait IdentityTrait
{

    public static $statusActive = 1;
    public static $statusInactive = 0;
    public $statusAttribute = 'status';
    private $_statusRules = [];
    public $authKeyAttribute = 'auth_key';
    private $_authKeyRules = [];
    public $accessTokenAttribute = 'access_token';
    private $_accessTokenRules = [];

    /**
     * 
     * @param string|integer $id
     * @return type
     */
    public static function findIdentity($id)
    {
        $self = static::buildNoInitModel();
        return static::findOne([$self->idAttribute => $id]);
    }

    /**
     * 
     * @param string $guid
     * @return type
     */
    public static function findIdentityByGuid($guid)
    {
        return static::findOne($guid);
    }

    /**
     * 
     * @param string $token
     * @param type $type
     * @return type
     */
    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        $self = static::buildNoInitModel();
        return static::findOne([$self->accessTokenAttribute => $token]);
    }

    /**
     * 
     * @return string|null
     */
    public function getAuthKey()
    {
        $authKeyAttribute = $this->authKeyAttribute;
        return is_string($authKeyAttribute) ? $this->$authKeyAttribute : null;
    }

    /**
     * 
     * @param string $key
     * @return string
     */
    public function setAuthKey($key)
    {
        $authKeyAttribute = $this->authKeyAttribute;
        return is_string($authKeyAttribute) ? $this->$authKeyAttribute = $key : null;
    }

    /**
     * 
     * @param string $authKey
     * @return string
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 
     * @return array
     */
    public function getAuthKeyRules()
    {
        if (empty($this->_authKeyRules)) {
            $this->_authKeyRules = [
                [[$this->authKeyAttribute], 'required'],
                [[$this->authKeyAttribute], 'string', 'max' => 40],
            ];
        }
        return $this->_authKeyRules;
    }

    /**
     * 
     * @param array $rules
     */
    public function setAuthKeyRules($rules)
    {
        if (!empty($rules) && is_array($rules)) {
            $this->_authKeyRules = $rules;
        }
    }

    /**
     * 
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onInitAuthKey($event)
    {
        $sender = $event->sender;
        $authKeyAttribute = $sender->authKeyAttribute;
        $sender->$authKeyAttribute = sha1(Yii::$app->security->generateRandomString());
    }

    /**
     * 
     * @return string|null
     */
    public function getAccessToken()
    {
        $accessTokenAttribute = $this->accessTokenAttribute;
        return is_string($accessTokenAttribute) ? $this->$accessTokenAttribute : null;
    }

    /**
     * 
     * @param string $token
     * @return string|null
     */
    public function setAccessToken($token)
    {
        $accessTokenAttribute = $this->accessTokenAttribute;
        return is_string($accessTokenAttribute) ? $this->$accessTokenAttribute = $token : null;
    }

    /**
     * 
     * @return array
     */
    public function getAccessTokenRules()
    {
        if (empty($this->_accessTokenRules)) {
            $this->_accessTokenRules = [
                [[$this->accessTokenAttribute], 'required'],
                [[$this->accessTokenAttribute], 'string', 'max' => 40],
            ];
        }
        return $this->_accessTokenRules;
    }

    /**
     * 
     * @param array $rules
     */
    public function setAccessTokenRules($rules)
    {
        if (!empty($rules) && is_array($rules)) {
            $this->_accessTokenRules = $rules;
        }
    }

    /**
     * 
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onInitAccessToken($event)
    {
        $sender = $event->sender;
        $accessTokenAttribute = $sender->accessTokenAttribute;
        $sender->$accessTokenAttribute = sha1(Yii::$app->security->generateRandomString());
    }

    /**
     * 
     * @return integer
     */
    public function getStatus()
    {
        $statusAttribute = $this->statusAttribute;
        return is_string($statusAttribute) ? $this->$statusAttribute : null;
    }

    /**
     * 
     * @param integer $status
     * @return integer|null
     */
    public function setStatus($status)
    {
        $statusAttribute = $this->statusAttribute;
        return is_string($statusAttribute) ? $this->$statusAttribute = $status : null;
    }

    /**
     * 
     * @return array
     */
    public function getStatusRules()
    {
        if (empty($this->_statusRules)) {
            $this->_statusRules = [
                [[$this->statusAttribute], 'required'],
                [[$this->statusAttribute], 'number', 'integerOnly' => true, 'min' => 0],
            ];
        }
        return $this->_statusRules;
    }

    /**
     * 
     * @param array $rules
     */
    public function setStatusRules($rules)
    {
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
    public function onInitStatusAttribute($event)
    {
        $sender = $event->sender;
        $statusAttribute = $sender->statusAttribute;
        $sender->$statusAttribute = self::$statusActive;
    }
}
