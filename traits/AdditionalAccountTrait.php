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
 * Additional account features.
 * @property boolean $canBeLogon determines whether this account could be used for logging-in.
 * @property-read array $enableLoginAttributeRules
 * @property-read array $additionalAccountRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait AdditionalAccountTrait
{

    use PasswordTrait;

    /**
     * @var boolean|string The attribute of which determines whether enable to
     * login with current additional account. You can assign it to false ff you
     * want to disable this feature, this is equivolent to not allow to login
     * with current additional account among all the users.
     */
    public $enableLoginAttribute = false;

    /**
     * @var boolean|string  Determines whether login with current additional
     * account with an independent password or not. If you set $enableLoginAttribute
     * to false, this feature will be skipped.
     */
    public $independentPassword = false;

    /**
     * 
     * @return boolean
     */
    public function getCanBeLogon()
    {
        if (!$this->enableLoginAttribute) {
            return false;
        }
        $enableLoginAttribute = $this->enableLoginAttribute;
        return $this->$enableLoginAttribute > 0;
    }

    /**
     * 
     * @param boolean $can
     * @return integer
     */
    public function setCanBeLogon($can)
    {
        if (!$this->enableLoginAttribute) {
            return;
        }
        $enableLoginAttribute = $this->enableLoginAttribute;
        $this->$enableLoginAttribute = ($can ? 1 : 0);
    }

    /**
     * 
     * @return array
     */
    public function getEnableLoginAttributeRules()
    {
        return $this->enableLoginAttribute && is_string($this->enableLoginAttribute) ? [
            [[$this->enableLoginAttribute], 'boolean'],
            [[$this->enableLoginAttribute], 'default', 'value' => true],
                ] : [];
    }

    /**
     * 
     * @return array
     */
    public function getAdditionalAccountRules()
    {
        $rules = $this->getEnableLoginAttributeRules();
        if ($this->independentPassword) {
            $rules = array_merge($rules, $this->getPasswordHashRules());
        }
        return $rules;
    }
}
