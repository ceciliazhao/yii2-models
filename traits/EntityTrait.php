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
 * This trait must be used in class extended from ActiveRecord.
 * @property array $entityRules
 * @property array $entityBehaviors
 */
trait EntityTrait {

    use GUIDTrait,
        IDTrait,
        IPTrait,
        TimestampTrait;

    private $_entityRules = [];
    private $_entityBehaviors = [];
    public static $EVENT_NEW_RECORD_CREATED = 'newRecordCreated';

    /**
     * Populate and return the entity rules.
     * You should call this function in your extended class and merge the result
     * with your rules, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function rules() {
        return $this->entityRules;
    }

    /**
     * Populate and return the entity behaviors.
     * You should call this function in your extended class and merge the result
     * with your behaviors, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function behaviors() {
        return $this->entityBehaviors;
    }

    /**
     * 
     * @return type
     */
    public function getEntityRules() {
        if (empty($this->_entityRules) || !is_array($this->_entityRules)) {
            $this->_entityRules = array_merge(
                    $this->GUIDRules, $this->IDRules, $this->CreatedAtRules, $this->UpdatedAtRules, $this->IPRules
            );
        }
        return $this->_entityRules;
    }

    /**
     * 
     * @param type $rules
     */
    public function setEntityRules($rules = []) {
        $this->_entityRules = $rules;
    }

    /**
     * 
     * @return type
     */
    public function getEntityBehaviors() {
        if (empty($this->_entityBehaviors) || !is_array($this->_entityBehaviors)) {
            $this->_entityBehaviors = $this->timestampBehaviors;
        }
        return $this->_entityBehaviors;
    }

    /**
     * 
     * @return type
     */
    public function setEntityBehaviors() {
        return $this->_entityBehaviors;
    }

    /**
     * 
     * @param type $cacheKey
     * @param type $value
     * @return type
     */
    public static function resetCacheKey($cacheKey, $value = false) {
        return Yii::$app->cache->set($cacheKey, $value);
    }

}
