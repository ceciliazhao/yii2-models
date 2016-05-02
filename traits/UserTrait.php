<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace vistart\Models\traits;

/**
 * Assemble PasswordTrait, RegistrationTrait and IdentityTrait into UserTrait.
 * This trait can only be used in the class extended from [[BaseEntityModel]],
 * [[BaseMongoEntityModel]], [[BaseRedisEntityModel]], or any other classes used
 * [[EntityTrait]].
 * This trait implements two methods `create()` and `findOneOrCreate()`.
 * Please read the notes of methods and used traits for further detailed usage.
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
     * Create new entity model associated with current user. The model to be created
     * must be extended from [[BaseBlameableModel]], [[BaseMongoBlameableModel]],
     * [[BaseRedisBlameableModel]], or any other classes used [[BlameableTrait]].
     * if $config does not specify `userClass` property, self will be assigned to.
     * @param string $className Full qualified class name.
     * @param array $config name-value pairs that will be used to initialize
     * the object properties.
     * @param boolean $loadDefault Determines whether loading default values
     * after entity model created.
     * Notice! The [[\yii\mongodb\ActiveRecord]] and [[\yii\redis\ActiveRecord]]
     * does not support loading default value. If you want to assign properties
     * with default values, please define the `default` rule(s) for properties in
     * `rules()` method and return them by yourself if you don't specified them in config param.
     * @param boolean $skipIfSet whether existing value should be preserved.
     * This will only set defaults for attributes that are `null`.
     * @return [[$className]] new model created with specified configuration.
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
     * Find existed, or create new model.
     * If model to be found doesn't exist, and $config is null, the parameter
     * `$condition` will be regarded as properties of new model.
     * If you want to know whether the returned model is new model, please check 
     * the return value of `getIsNewRecord()` method.
     * @param string $className Full qualified class name.
     * @param array $condition Search condition, or properties if not found and
     * $config is null.
     * @param array $config new model's configuration array. If you specify this
     * parameter, the $condition will be skipped when created one.
     * @return [[$className]] the existed model, or new model created by specified
     * condition or configuration.
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
            parent::rules(), $this->passwordHashRules, $this->passwordResetTokenRules, $this->sourceRules, $this->statusRules, $this->authKeyRules, $this->accessTokenRules
        );
    }

    /**
     * @var string[] Subsidiary map.
     * Array key represents class alias,
     * array value represents the full qualified class name corresponds to the alias.
     * 
     * For example:
     * ```php
     * public $subsidiaryMap = [
     *     'Profile' => 'app\models\user\Profile',
     * ];
     * ```
     * 
     * If you want to create subsidiary model and the class is not found, the array elements will be taken.
     * @see normalizeSubsidiaryClass
     * @since 2.1
     */
    public $subsidiaryMap = [
    ];

    /**
     * Get full-qualified subsidiary class name.
     * @param string $class Subsidiary class name or alias.
     * If this parameter is empty or not a string, `null` will ge returned.
     * If `$class` exists, then it will be returned directly.
     * If not, it will search the subsidiary map. then return it if found.
     * If not yet, it will check whether `$class` exists in current namespace,
     * then return it if found.
     * @return string|null Full-qualified class name.
     * @since 2.1
     */
    public function normalizeSubsidiaryClass($class)
    {
        if (empty($class) || !is_string($class)) {
            return null;
        }
        if (!class_exists($class)) {
            if (array_key_exists($class, $this->subsidiaryMap)) {
                $class = $this->subsidiaryMap[$class];
            } else {
                return null;
            }
        }
        return $class;
    }

    /**
     * Call `create*` method.
     * If prefix of method name is `create`, then it will be regarded as 'creating subsidiary model`.
     * The rest of it will be regareded as class name or alias.
     * 
     * You can access it like following:
     * ```php
     * $profile = $user->createProfile();
     * ```
     * If `Profile` exists, then it will return.
     * If not, then it will search the subsidiary map or `user`'s namespace, then it will return if found.
     * 
     * @inheritdoc
     * @param mixed $name
     * @param mixed $params
     * @return mixed
     * @since 2.1
     */
    public function __call($name, $params)
    {
        if (strpos(strtolower($name), "create") === 0) {
            $class = substr($name, 6);
            $config = (isset($params) && isset($params[0])) ? $params[0] : [];
            return $this->createSubsidiary($class, $config);
        }
        return parent::__call($name, $params);
    }

    /**
     * Create subsidiary model.
     * @param string $class Subsidiary class name or alias.
     * @param array $config
     * @return mixed
     * @since 2.1
     */
    public function createSubsidiary($class, $config = [])
    {
        $class = $this->normalizeSubsidiaryClass($class);
        if (empty($class)) {
            return null;
        }
        return $this->create($class, $config);
    }
}
