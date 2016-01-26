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
 * Description of SelfBlameableTrait
 *
 * @property-read static $parent
 * @property-read array $children
 * @property-read array $oldChildren
 * @property array $selfBlameableRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait SelfBlameableTrait
{

    /**
     * @var false|string attribute name of which store the parent's guid.
     */
    public $parentAttribute = false;

    /**
     * @var string|array rule name and parameters of parent attribute, as well
     * as self referenced ID attribute.
     */
    public $parentAttributeRule = ['string', 'max' => 36];

    /**
     * @var string self referenced ID attribute.
     */
    public $refIdAttribute = 'guid';
    public static $parentNone = 0;
    public static $parentParent = 1;
    public static $parentTypes = [
        0 => 'none',
        1 => 'parent',
    ];
    public static $onNoAction = 0;
    public static $onRestrict = 1;
    public static $onCascade = 2;
    public static $onSetNull = 3;
    public static $onUpdateTypes = [
        0 => 'on action',
        1 => 'restrict',
        2 => 'cascade',
        3 => 'set null',
    ];

    /**
     * @var integer indicates the on delete type. default to cascade.
     */
    public $onDeleteType = 2;

    /**
     * @var integer indicates the on update type. default to cascade.
     */
    public $onUpdateType = 2;

    /**
     * @var boolean indicates whether throw exception or not when restriction occured on updating or deleting operation.
     */
    public $throwRestrictException = false;
    private $selfLocalBlameableRules = [];

    /**
     * Get rules associated with self blameable attribute.
     * @return array rules.
     */
    public function getSelfBlameableRules()
    {
        if (!is_string($this->parentAttribute)) {
            return [];
        }
        if (empty($this->selfLocalBlameableRules) || !is_array($this->selfLocalBlameableRules)) {
            return $this->selfLocalBlameableRules;
        }
        if (is_string($this->parentAttributeRule)) {
            $this->parentAttributeRule = [$this->parentAttributeRule];
        }
        $this->selfLocalBlameableRules = [
            array_merge([$this->parentAttribute], $this->parentAttributeRule),
        ];
        return $this->selfLocalBlameableRules;
    }

    /**
     * Set rules associated with self blameable attribute.
     * @param array $rules rules.
     */
    public function setSelfBlameableRules($rules = [])
    {
        $this->selfLocalBlameableRules = $rules;
    }

    /**
     * Bear a child.
     * @param array $config
     * @return static
     */
    public function bear($config = [])
    {
        if (isset($config['class'])) {
            unset($config['class']);
        }
        $refIdAttribute = $this->refIdAttribute;
        $config[$this->parentAttribute] = $this->$refIdAttribute;
        return new static($config);
    }

    /**
     * Event triggered before deleting itself.
     * @param \yii\base\ModelEvent $event
     * @return boolean true if parentAttribute not specified.
     * @throws \yii\db\IntegrityException throw if $throwRestrictException is true when $onDeleteType is on restrict.
     */
    public function onDeleteChildren($event)
    {
        $sender = $event->sender;
        if (!is_string($sender->parentAttribute)) {
            return true;
        }
        switch ($sender->onDeleteType) {
            case static::$onRestrict:
                $event->isValid = $sender->children === null;
                if ($this->throwRestrictException) {
                    throw new \yii\db\IntegrityException('Delete restricted.');
                }
                break;
            case static::$onCascade:
                $event->isValid = $sender->deleteChildren();
                break;
            case static::$onSetNull:
                $event->isValid = $sender->updateChildren(null);
                break;
            case static::$onNoAction:
            default:
                $event->isValid = true;
                break;
        }
    }

    /**
     * Event triggered before updating itself.
     * @param \yii\base\ModelEvent $event
     * @return boolean true if parentAttribute not specified.
     * @throws \yii\db\IntegrityException throw if $throwRestrictException is true when $onUpdateType is on restrict.
     */
    public function onUpdateChildren($event)
    {
        $sender = $event->sender;
        if (!is_string($sender->parentAttribute)) {
            return true;
        }
        switch ($sender->onUpdateType) {
            case static::$onRestrict:
                $event->isValid = $sender->getOldChildren() === null;
                if ($this->throwRestrictException) {
                    throw new \yii\db\IntegrityException('Update restricted.');
                }
                break;
            case static::$onCascade:
                $event->isValid = $sender->updateChildren();
                break;
            case static::$onSetNull:
                $event->isValid = $sender->updateChildren(null);
                break;
            case static::$onNoAction:
            default:
                $event->isValid = true;
                break;
        }
    }

    /**
     * Get parent query.
     * Or get parent instance if access by magic property.
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(static::className(), [$this->refIdAttribute => $this->parentAttribute]);
    }

    /**
     * Get children query.
     * Or get children instances if access magic property.
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(static::className(), [$this->parentAttribute => $this->refIdAttribute])->inverseOf('parent');
    }

    /**
     * Get children which parent attribute point to the my old guid.
     * @return static[]
     */
    public function getOldChildren()
    {
        return static::find()->where([$this->parentAttribute => $this->getOldAttribute($this->refIdAttribute)])->all();
    }

    /**
     * Update all children, not grandchildren.
     * If onUpdateType is on cascade, the children will be updated automatically.
     * @param mixed $value set guid if false, set empty string if empty() return
     * true, otherwise set it to $parentAttribute.
     * @return \yii\db\IntegrityException|boolean true if all update operations
     * succeeded to execute, or false if anyone of them failed. If not production
     * environment or enable debug mode, it will return exception.
     * @throws \yii\db\IntegrityException throw if anyone update failed.
     */
    public function updateChildren($value = false)
    {
        $children = $this->getOldChildren();
        if (empty($children)) {
            return true;
        }
        $parentAttribute = $this->parentAttribute;
        $transaction = $this->getDb()->beginTransaction();
        try {
            foreach ($children as $child) {
                if ($value === false) {
                    $refIdAttribute = $this->refIdAttribute;
                    $child->$parentAttribute = $this->$refIdAttribute;
                } elseif (empty($value)) {
                    $child->$parentAttribute = '';
                } else {
                    $child->$parentAttribute = $value;
                }
                if (!$child->save()) {
                    throw new \yii\db\IntegrityException('Update failed:' . $child->errors);
                }
            }
            $transaction->commit();
        } catch (\yii\db\IntegrityException $ex) {
            $transaction->rollBack();
            if (YII_DEBUG || YII_ENV !== YII_ENV_PROD) {
                Yii::error($ex->errorInfo, static::className() . '\update');
                return $ex;
            }
            Yii::warning($ex->errorInfo, static::className() . '\update');
            return false;
        }
        return true;
    }

    /**
     * Delete all children, not grandchildren.
     * If onDeleteType is on cascade, the children will be deleted automatically.
     * If onDeleteType is on restrict and contains children, the deletion will
     * be restricted.
     * @return \yii\db\IntegrityException|boolean true if all delete operations
     * succeeded to execute, or false if anyone of them failed. If not production
     * environment or enable debug mode, it will return exception.
     * @throws \yii\db\IntegrityException throw if anyone delete failed.
     */
    public function deleteChildren()
    {
        $children = $this->children;
        if (empty($children)) {
            return true;
        }
        $transaction = $this->getDb()->beginTransaction();
        try {
            foreach ($children as $child) {
                if (!$child->delete()) {
                    throw new \yii\db\IntegrityException('Delete failed:' . $child->errors);
                }
            }
            $transaction->commit();
        } catch (\yii\db\IntegrityException $ex) {
            $transaction->rollBack();
            if (YII_DEBUG || YII_ENV !== YII_ENV_PROD) {
                Yii::error($ex->errorInfo, static::className() . '\delete');
                return $ex;
            }
            Yii::warning($ex->errorInfo, static::className() . '\delete');
            return false;
        }
        return true;
    }

    /**
     * Update children's parent attribute.
     * Event triggered before updating.
     * @param \yii\base\ModelEvent $event
     * @return boolean
     */
    public function onParentRefIdChanged($event)
    {
        $sender = $event->sender;
        if ($sender->isAttributeChanged($sender->refIdAttribute)) {
            return $sender->onUpdateChildren($event);
        }
    }

    protected function initSelfBlameableEvents()
    {
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'onParentRefIdChanged']);
        $this->on(static::EVENT_BEFORE_DELETE, [$this, 'onDeleteChildren']);
    }
}
