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
 * Description of PasswordTrait
 * @property-write string $password New password to be set.
 * @property array $passwordHashRules
 * @property array $rules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait PasswordTrait {

    public static $EVENT_AFTER_SET_PASSWORD = "afterSetPassword";
    public static $EVENT_BEFORE_VALIDATE_PASSWORD = "beforeValidatePassword";
    public static $EVENT_VALIDATE_PASSWORD_SUCCEEDED = "validatePasswordSucceeded";
    public static $EVENT_VALIDATE_PASSWORD_FAILED = "validatePasswordFailed";

    /**
     * @var string The name of attribute used for storing password hash.
     */
    public $passwordHashAttribute = 'pass_hash';

    /**
     * @var integer Cost parameter used by the Blowfish hash algorithm.
     */
    public $passwordCost = 13;

    /**
     * @var string strategy, which should be used to generate password hash.
     * Available strategies:
     * - 'password_hash' - use of PHP `password_hash()` function with PASSWORD_DEFAULT algorithm.
     *   This option is recommended, but it requires PHP version >= 5.5.0
     * - 'crypt' - use PHP `crypt()` function.
     */
    public $passwordHashStrategy = 'crypt';

    /**
     * @var integer if $passwordHashStrategy equals 'crypt', this value statically
     * equals 60.
     */
    public $passwordHashAttributeLength = 60;
    private $_passwordHashRules = [];

    /**
     * Get rules of password hash.
     * @return array password hash rules.
     */
    public function getPasswordHashRules() {
        if ($this->passwordHashStrategy == 'crypt') {
            $this->passwordHashAttributeLength = 60;
        }
        if (empty($this->_passwordHashRules)) {
            $this->_passwordHashRules = [
                [[$this->passwordHashAttribute], 'string', 'max' => $this->passwordHashAttributeLength],
            ];
        }
        return $this->_passwordHashRules;
    }

    /**
     * Set rules of password hash.
     * @param array $rules password hash rules.
     */
    public function setPasswordHashRules($rules) {
        if (!empty($rules) && is_array($rules)) {
            $this->_passwordHashRules = $rules;
        }
    }

    /**
     * Generates a secure hash from a password and a random salt.
     *
     * The generated hash can be stored in database.
     * Later when a password needs to be validated, the hash can be fetched and passed
     * to [[validatePassword()]]. For example,
     *
     * ~~~
     * // generates the hash (usually done during user registration or when the password is changed)
     * $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
     * // ...save $hash in database...
     *
     * // during login, validate if the password entered is correct using $hash fetched from database
     * if (Yii::$app->getSecurity()->validatePassword($password, $hash) {
     *     // password is good
     * } else {
     *     // password is bad
     * }
     * ~~~
     *
     * @param string $password The password to be hashed.
     * @return string The password hash string. When [[passwordHashStrategy]] is set to 'crypt',
     * the output is always 60 ASCII characters, when set to 'password_hash' the output length
     * might increase in future versions of PHP (http://php.net/manual/en/function.password-hash.php)
     */
    public function generatePasswordHash($password) {
        Yii::$app->security->passwordHashStrategy = $this->passwordHashStrategy;
        return Yii::$app->security->generatePasswordHash($password, $this->cost);
    }

    /**
     * Verifies a password against a hash.
     * 
     * @param string $password The password to verify.
     * @return boolean whether the password is correct.
     */
    public function validatePassword($password) {
        $passwordHashAttribute = $this->passwordHashAttribute;
        $result = Yii::$app->security->validatePassword($password, $this->$passwordHashAttribute);
        if ($result) {
            $this->trigger(self::$EVENT_VALIDATE_PASSWORD_SUCCEEDED);
        } else {
            $this->trigger(self::$EVENT_VALIDATE_PASSWORD_FAILED);
        }
        return $result;
    }

    /**
     * Set new password.
     * @param string $password the new password to be set.
     */
    public function setPassword($password) {
        $passwordHashAttribute = $this->passwordHashAttribute;
        $this->$passwordHashAttribute = Yii::$app->security->generatePasswordHash($password);
        $this->trigger(self::$EVENT_AFTER_SET_PASSWORD);
    }

}