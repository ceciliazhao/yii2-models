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
 * This trait must be used in class extended from ActiveRecord. The ActiveRecord
 * supports \yii\db\ActiveRecord, \yii\mongodb\ActiveRecord, \yii\redis\ActiveRecord.
 * @property array $entityRules
 * @property array $entityBehaviors
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait EntityTrait
{

    use GUIDTrait,
        IDTrait,
        IPTrait,
        TimestampTrait;

    private $entityLocalRules = [];
    private $entityLocalBehaviors = [];

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
     * @var string the name of query class or sub-class.
     */
    public $queryClass;

    /**
     * @return \static New self without any initializations.
     */
    public static function buildNoInitModel()
    {
        return new static(['skipInit' => true]);
    }

    /**
     * Populate and return the entity rules.
     * You should call this function in your extended class and merge the result
     * with your rules, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function rules()
    {
        return $this->getEntityRules();
    }

    /**
     * Populate and return the entity behaviors.
     * You should call this function in your extended class and merge the result
     * with your behaviors, instead of overriding it, unless you know the
     * consequences.
     * @return type
     */
    public function behaviors()
    {
        return $this->getEntityBehaviors();
    }

    /**
     * Get cache component. If cache component is not configured, null will be
     * given.
     * @return \yii\caching\Cache cache component.
     */
    protected function getCache()
    {
        $cacheId = $this->cacheId;
        return empty($cacheId) ? null : Yii::$app->$cacheId;
    }

    /**
     * Get entity rules cache key.
     * @return string cache key.
     */
    public function getEntityRulesCacheKey()
    {
        return static::className() . $this->cachePrefix . static::$cacheKeyEntityRules;
    }

    /**
     * Get entity rules cache tag.
     * @return string cache tag.
     */
    public function getEntityRulesCacheTag()
    {
        return static::className() . $this->cachePrefix . static::$cacheTagEntityRules;
    }

    /**
     * Get entity rules.
     * @return array rules.
     */
    public function getEntityRules()
    {
        $cache = $this->getCache();
        if ($cache)
        {
            $this->entityLocalRules = $cache->get($this->getEntityRulesCacheKey());
        }
        if (empty($this->entityLocalRules) || !is_array($this->entityLocalRules))
        {
            $rules = array_merge($this->getGuidRules(), $this->getIdRules(), $this->getCreatedAtRules(), $this->getUpdatedAtRules(), $this->getIpRules());
            $this->setEntityRules($rules);
        }
        return $this->entityLocalRules;
    }

    /**
     * Set entity rules.
     * @param array $rules.
     */
    protected function setEntityRules($rules = [])
    {
        $this->entityLocalRules = $rules;
        $cache = $this->getCache();
        if ($cache)
        {
            $tagDependency = new \yii\caching\TagDependency(
                    ['tags' =>
                [$this->getEntityRulesCacheTag()]
                    ]
            );
            $cache->set($this->getEntityRulesCacheKey(), $rules, 0, $tagDependency);
        }
    }

    /**
     * Get entity behaviors cache key.
     * @return string cache key.
     */
    public function getEntityBehaviorsCacheKey()
    {
        return static::className() . $this->cachePrefix . static::$cacheKeyEntityBehaviors;
    }

    /**
     * Get entity behaviors cache tag.
     * @return string cache tag.
     */
    public function getEntityBehaviorsCacheTag()
    {
        return static::className() . $this->cachePrefix . static::$cacheTagEntityBehaviors;
    }

    /**
     * Get the entity behaviors.
     * @return array
     */
    public function getEntityBehaviors()
    {
        $cache = $this->getCache();
        if ($cache)
        {
            $this->entityLocalBehaviors = $cache->get($this->getEntityBehaviorsCacheKey());
        }
        if (empty($this->entityLocalBehaviors) || !is_array($this->entityLocalBehaviors))
        {
            $this->setEntityBehaviors($this->getTimestampBehaviors());
        }
        return $this->entityLocalBehaviors;
    }

    /**
     * Set the entity behaviors.
     * @param array $behaviors
     */
    protected function setEntityBehaviors($behaviors)
    {
        $this->entityLocalBehaviors = $behaviors;
        $cache = $this->getCache();
        if ($cache)
        {
            $tagDependencyConfig = ['tags' => [$this->getEntityBehaviorsCacheTag()]];
            $tagDependency = new \yii\caching\TagDependency($tagDependencyConfig);
            $cache->set($this->getEntityBehaviorsCacheKey(), $behaviors, 0, $tagDependency);
        }
    }

    /**
     * Reset cache key.
     * @param string $cacheKey
     * @param mixed $value
     * @return boolean whether the value is successfully stored into cache. if
     * cache component was not configured, then return false directly.
     */
    public function resetCacheKey($cacheKey, $value = false)
    {
        $cache = $this->getCache();
        if ($cache)
        {
            return $this->getCache()->set($cacheKey, $value);
        }
        return false;
    }

    /**
     * Attach events associated with entity model.
     */
    protected function initEntityEvents()
    {
        $this->on(static::EVENT_INIT, [$this, 'onInitCache']);
        $this->attachInitGuidEvent(static::$eventNewRecordCreated);
        $this->attachInitIdEvent(static::$eventNewRecordCreated);
        $this->attachInitIpEvent(static::$eventNewRecordCreated);
        if ($this->isNewRecord)
        {
            $this->trigger(static::$eventNewRecordCreated);
        }
    }

    /**
     * Initialize the cache prefix.
     * @param \yii\base\Event $event
     */
    public function onInitCache($event)
    {
        $sender = $event->sender;
        $data = $event->data;
        if (isset($data['prefix']))
        {
            $sender->cachePrefix = $data['prefix'];
        } else
        {
            $sender->cachePrefix = $sender::className();
        }
    }

    /**
     * Record warnings.
     */
    protected function recordWarnings()
    {
        if (YII_ENV !== YII_ENV_PROD || YII_DEBUG)
        {
            Yii::warning($this->errors);
        }
    }

    /**
     * Get guid or id. if neither disabled, return null.
     * @return string
     */
    public function __toString()
    {
        if (is_string($this->guidAttribute))
        {
            return $this->guid;
        }
        if (is_string($this->idAttribute))
        {
            return $this->id;
        }
        return null;
    }

    /**
     * @inheritdoc
     * -------------
     * if enable `$idAttribute` and $row[$idAttribute] set, the `idPreassigned`
     * will be assigned to true.
     */
    public static function instantiate($row)
    {
        $self = static::buildNoInitModel();
        if (isset($self->idAttribute) && isset($row[$self->idAttribute]))
        {
            return new static(['idPreassigned' => true]);
        }
        return new static;
    }
}
