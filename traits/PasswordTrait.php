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
 * Description of PasswordTrait
 * @property-write string $password New password to be set.
 * @property array $passwordHashRules
 * @property array $rules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait PasswordTrait
{
    public static $EVENT_AFTER_SET_PASSWORD = "afterSetPassword";
    public static $EVENT_BEFORE_VALIDATE_PASSWORD = "beforeValidatePassword";
    public static $EVENT_VALIDATE_PASSWORD_SUCCEEDED = "validatePasswordSucceeded";
    public static $EVENT_VALIDATE_PASSWORD_FAILED = "validatePasswordFailed";
    
    /**
     * @var string The name of attribute used for storing password hash.
     */
    public $passwordHashAttribute = 'pass_hash';
    private $_passwordHashRules = [];
    
    /**
     * 
     * @return array
     */
    public function getPasswordHashRules()
    {
        if (empty($this->_passwordHashRules)) {
            $this->_passwordHashRules = [
                [[$this->passwordHashAttribute], 'string', 'max' => 80],
            ];
            return $this->_passwordHashRules;
        }
        return $this->_passwordHashRules;
    }
    
    /**
     * 
     * @param array $rules
     */
    public function setPasswordHashRules($rules)
    {
        if (!empty($rules) && is_array($rules)){
            $this->_passwordHashRules = $rules;
        }
    }
    
    /**
     * 
     * @param string $password
     * @param integer $cost
     * @return string
     */
    public function generatePasswordHash($password, $cost = 13)
    {
        return Yii::$app->security->generatePasswordHash($password, $cost);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $passwordHashAttribute = $this->passwordHashAttribute;
        $result = Yii::$app->security->validatePassword($password, $this->$passwordHashAttribute);
        if ($result)
        {
            $this->trigger(self::$EVENT_VALIDATE_PASSWORD_SUCCEEDED);
        } else {
            $this->trigger(self::$EVENT_VALIDATE_PASSWORD_FAILED);
        }
        return $result;
    }
    
    /**
     * 
     * @param string $password
     */
    public function setPassword($password)
    {
        $passwordHashAttribute = $this->passwordHashAttribute;
        $this->$passwordHashAttribute = Yii::$app->security->generatePasswordHash($password);
        $this->trigger(self::$EVENT_AFTER_SET_PASSWORD);
    }
}
