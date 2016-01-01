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
 * Assemble PasswordTrait, RegistrationTrait and IdentityTrait into UserTrait.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserTrait
{
    use PasswordTrait, RegistrationTrait, IdentityTrait;
    
    public function init()
    {
        if ($this->skipInit) return;
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, 'onInitStatusAttribute']);
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, 'onInitSourceAttribute']);
        parent::init();
    }
    
    /**
     * 
     * @param string $className Full qualified name.
     * @param array $config
     */
    public function createNewModel($className, $config = [])
    {
        $guidAttribute = $this->guidAttribute;
        if (!isset($config[$guidAttribute])){
            $config[$guidAttribute] = $this->$guidAttribute;
        }
        return new $className($config);
    }
}
