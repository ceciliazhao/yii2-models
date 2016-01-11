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
 * @property boolean $canBeLogon
 */
trait AdditionalAccountTrait {

    use PasswordTrait;

    /**
     * @var boolean|string 
     */
    public $enableLoginAttribute = false;

    /**
     * @var boolean|string 
     */
    public $independentPassword = false;

    public function getCanBeLogon() {
        if (!$this->enableLoginAttribute) {
            return false;
        }
        $enableLoginAttribute = $this->enableLoginAttribute;
        return $this->$enableLoginAttribute > 0;
    }

    public function setCanBeLogon($can) {
        if (!$this->enableLoginAttribute) {
            return;
        }
        $enableLoginAttribute = $this->enableLoginAttribute;
        $this->$enableLoginAttribute = ($can ? 1 : 0);
    }

    public function getEnableLoginAttributeRules() {
        return $this->enableLoginAttribute && is_string($this->enableLoginAttribute) ? [
            [[$this->enableLoginAttribute], 'boolean'],
            [[$this->enableLoginAttribute], 'default', 'value' => true],
                ] : [];
    }

    public function getAdditionalAccountRules() {
        return array_merge($this->getEnableLoginAttributeRules(), $this->getPasswordHashRules());
    }

}
