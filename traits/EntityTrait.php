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

    /**
     * @var string cache key and tag prefix. the prefix is usually set to full
     * qualified class name. 
     */
    public $cachePrefix = '';
    public static $eventNewRecordCreated = 'newRecordCreated';
    public static $cacheKeyEntityRules = 'entity_rules';
    public static $cacheTagEntityRules = 'tag_entity_rules';
    public static $cacheKeyEntityBehaviors = 'entity_behaviors';
    public static $cacheTagEntityBehaviors = 'tag_entity_behaviors';

    /**
     * @var string cache component id. 
     */
    public $cacheId = 'cache';

    /**
     * @var boolean Determines to skip initialization.
     */
    public $skipInit = false;

    /**
     * Populate and return the entity rules.
     * You should call this function in your extended class and merge the result
     * with your rules, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function rules() {
        return $this->getEntityRules();
    }

    /**
     * Populate and return the entity behaviors.
     * You should call this function in your extended class and merge the result
     * with your behaviors, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function behaviors() {
        return $this->getEntityBehaviors();
    }

    /**
     * Get cache component. If cache component is not configured, null will be
     * given.
     * @return \yii\caching\Cache cache component.
     */
    protected function getCache() {
        $cacheId = $this->cacheId;
        return empty($cacheId) ? null : Yii::$app->$cacheId;
    }

    /**
     * 
     * @return array
     */
    public function getEntityRules() {
        $cache = $this->getCache();
        if ($cache) {
            $this->_entityRules = $cache->get($this->cachePrefix . static::$cacheKeyEntityRules);
        }
        if (empty($this->_entityRules) || !is_array($this->_entityRules)) {
            $rules = array_merge($this->getGuidRules(), $this->getIdRules(), $this->getCreatedAtRules(), $this->getUpdatedAtRules(), $this->getIpRules());
            $this->setEntityRules($rules);
        }
        return $this->_entityRules;
    }

    /**
     * 
     * @param array $rules
     */
    protected function setEntityRules($rules = []) {
        $this->_entityRules = $rules;
        $cache = $this->getCache();
        if ($cache) {
            $tagDependency = new \yii\caching\TagDependency(['tags' => [$this->cachePrefix . static::$cacheTagEntityRules]]);
            $cache->set($this->cachePrefix . static::$cacheKeyEntityRules, $rules, 0, $tagDependency);
        }
    }

    /**
     * 
     * @return array
     */
    public function getEntityBehaviors() {
        $cache = $this->getCache();
        if ($cache) {
            $this->_entityBehaviors = $cache->get($this->cachePrefix . static::$cacheKeyEntityBehaviors);
        }
        if (empty($this->_entityBehaviors) || !is_array($this->_entityBehaviors)) {
            $this->setEntityBehaviors($this->getTimestampBehaviors());
        }
        return $this->_entityBehaviors;
    }

    /**
     * 
     * @param array $behaviors
     */
    protected function setEntityBehaviors($behaviors) {
        $this->_entityBehaviors = $behaviors;
        $cache = $this->getCache();
        if ($cache) {
            $tagDependency = new \yii\caching\TagDependency(['tags' => [$this->cachePrefix . static::$cacheTagEntityBehaviors]]);
            $cache->set($this->cachePrefix . static::$cacheKeyEntityBehaviors, $behaviors, 0, $tagDependency);
        }
    }

    /**
     * Reset cache key.
     * @param string $cacheKey
     * @param mixed $value
     * @return boolean whether the value is successfully stored into cache. if
     * cache component was not configured, then return false directly.
     */
    public function resetCacheKey($cacheKey, $value = false) {
        $cache = $this->getCache();
        if ($cache) {
            return $this->getCache()->set($cacheKey, $value);
        }
        return false;
    }

    /**
     * 
     */
    protected function initEntityEvents() {
        $this->on(static::EVENT_INIT, [$this, 'onInitCache']);
        $this->on(static::$eventNewRecordCreated, [$this, 'onInitGuidAttribute']);
        $this->on(static::$eventNewRecordCreated, [$this, 'onInitIdAttribute']);
        $this->on(static::$eventNewRecordCreated, [$this, 'onInitIpAddress']);
        if ($this->isNewRecord) {
            $this->trigger(static::$eventNewRecordCreated);
        }
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onInitCache($event) {
        $sender = $event->sender;
        $data = $event->data;
        if (isset($data['prefix'])) {
            $sender->cachePrefix = $data['prefix'];
        } else {
            $sender->cachePrefix = $sender::className();
        }
    }

}
