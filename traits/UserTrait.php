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
 * Assemble PasswordTrait, RegistrationTrait and IdentityTrait into UserTrait.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserTrait
{
    use PasswordTrait,
        RegistrationTrait,
        IdentityTrait;

    /**
     * Create new entity model associated with current user.
     * if $config does not specify `userClass` property, self will be assigned to.
     * @param string $className Full qualified class name.
     * @param array $config name-value pairs that will be used to initialize
     * the object properties.
     * @param boolean $loadDefault Determines whether loading default values
     * after entity model created.
     * @param boolean $skipIfSet whether existing value should be preserved.
     * This will only set defaults for attributes that are `null`.
     * @return $className
     */
    public function create($className, $config = [], $loadDefault = true, $skipIfSet = true)
    {
        if (!isset($config['userClass'])) {
            $config['userClass'] = static::className();
        }
        if (isset($config['class'])) {
            unset($config['class']);
        }
        $entity = new $className($config);
        $createdByAttribute = $entity->createdByAttribute;
        $entity->$createdByAttribute = $this->guid;
        if ($loadDefault && method_exists($entity, 'loadDefaultValues')) {
            $entity->loadDefaultValues($skipIfSet);
        }
        return $entity;
    }

    /**
     * Find existed or create new model.
     * @param string $className
     * @param array $condition
     * @param array $config
     * @return $className
     */
    public function findOneOrCreate($className, $condition = [], $config = null)
    {
        $entity = new $className(['skipInit' => true]);
        if (!isset($condition[$entity->createdByAttribute])) {
            $condition[$entity->createdByAttribute] = $this->guid;
        }
        $model = $className::findOne($condition);
        if (!$model) {
            if ($config === null || !is_array($config)) {
                $config = $condition;
            }
            $model = $this->create($className, $config);
        }
        return $model;
    }

    /**
     * Get all rules with current user properties.
     * @return array all rules.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            $this->passwordHashRules,
            $this->passwordResetTokenRules,
            $this->sourceRules,
            $this->statusRules,
            $this->authKeyRules,
            $this->accessTokenRules
        );
    }
}
