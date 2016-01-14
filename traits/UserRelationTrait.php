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
 * @property array $groupGuids the guid array of all groups which owned by current relation.
 * @property boolean $isFavorite
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserRelationTrait {

    public $otherGuidAttribute = 'other_guid';

    /**
     * @var string the attribute name of which determines the `groups` field.
     * you can assign it to `false` if you want to disable group features.
     */
    public $groupsAttribute = 'groups';

    /**
     * @var string
     */
    public $remarkAttribute = 'remark';

    /**
     * @var string 
     */
    public $groupClass = '';

    /**
     * @var string the attribute name of which determines the relation type.
     */
    public $relationTypeAttribute = 'type';
    public static $relationTypeNormal = 0x00;
    public static $relationTypeSuspend = 0x01;
    public static $relationTypeBanned = 0x10;

    /**
     * @var array 
     */
    public $relationTypes = [
        0x00 => 'Normal',
        0x01 => 'Suspend',
        0x10 => 'Banned',
    ];

    /**
     * @var string the attribute name of which determines the `favorite` field.
     */
    public $favoriteAttribute = 'favorite';

    public function getIsFavorite() {
        $favoriteAttribute = $this->favoriteAttribute;
        return $this->$favoriteAttribute > 0;
    }

    public function setIsFavorite($fav) {
        $favoriteAttribute = $this->favoriteAttribute;
        $this->$favoriteAttribute = ($fav ? 1 : 0);
    }

    public function rules() {
        return array_merge(parent::rules(), $this->getUserRelationRules());
    }

    public function getUserRelationRules() {
        return array_merge([
            [[$this->relationTypeAttribute], 'boolean'],
            [[$this->relationTypeAttribute], 'default', 'value' => static::$relationTypeNormal],
                ], $this->getRemarkRules(), $this->getFavoriteRules(), $this->getGroupsRules(), $this->getOtherGuidRules());
    }

    public function getRemarkRules() {
        return is_string($this->remarkAttribute) ? [
            [[$this->remarkAttribute], 'string'],
            [[$this->remarkAttribute], 'default', 'value' => ''],
                ] : [];
    }

    public function getFavoriteRules() {
        return is_string($this->favoriteAttribute) ? [
            [[$this->favoriteAttribute], 'boolean'],
            [[$this->favoriteAttribute], 'default', 'value' => 1],
                ] : [];
    }

    public function getGroupsRules() {
        return is_string($this->groupsAttribute) ? [
            [[$this->groupsAttribute], 'required'],
            [[$this->groupsAttribute], 'string'],
            [[$this->groupsAttribute], 'default', 'value' => '[]'],
                ] : [];
    }

    public function getOtherGuidRules() {
        $rules = [
            [[$this->otherGuidAttribute, $this->relationTypeAttribute], 'required'],
            [[$this->otherGuidAttribute], 'string', 'max' => 36],
            [[$this->otherGuidAttribute, $this->createdByAttribute], 'unique', 'targetAttribute' => [$this->otherGuidAttribute, $this->createdByAttribute]],
        ];
        return $rules;
    }

    public function getGroupGuids() {
        $jsonParser = new \yii\web\JsonParser();
        $groupsAttribute = $this->groupsAttribute;
        if ($this->groupsAttribute === false) {
            return [];
        }
        return $jsonParser->parse($this->$groupsAttribute, true);
    }

    public function setGroupGuids($guids = []) {
        if (!is_array($guids) || $this->groupsAttribute === false) {
            return;
        }
        $guidArray = array_values($guids);
        $groupsAttribute = $this->groupsAttribute;
        $this->$groupsAttribute = json_encode($guidArray);
    }

    /**
     * Get group which guid is `$guid`.
     * @param string $guid
     * @return type
     */
    public function getGroup($guid) {
        if (empty($this->groupClass) || !is_string($this->groupClass) || $this->groupsAttribute === false) {
            return null;
        }
        $groupClass = $this->groupClass;
        return $groupClass::findOne($guid);
    }

    /**
     * Add group specified by `$guid`.
     * @param string $guid the guid of group to be added.
     */
    public function addGroup($guid) {
        if ($this->groupsAttribute === false) {
            return;
        }
        $groupGuids = $this->getGroupGuids();
        if (array_search($guid, $groupGuids) === false) {
            $groupGuids[] = $guid;
            $this->setGroupGuids($groupGuids);
        }
    }

    /**
     * Remove group specified by `$guid`.
     * @param string $guid the guid of group to be removed.
     */
    public function removeGroup($guid) {
        if ($this->groupsAttribute === false) {
            return;
        }
        $groupGuids = $this->getGroupGuids();
        if (($key = array_search($guid, $groupGuids)) !== false) {
            unset($groupGuids[$key]);
            $this->setGroupGuids($groupGuids);
        }
    }

    /**
     * 
     */
    public function removeAllGroups() {
        $this->setGroupGuids();
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onInitGroups($event) {
        $sender = $event->sender;
        $sender->removeAllGroups();
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onInitRemark($event) {
        $sender = $event->sender;
        $remarkAttribute = $sender->remarkAttribute;
        if (is_string($remarkAttribute)) {
            $sender->$remarkAttribute = '';
        }
    }

    /**
     * 
     */
    public function initUserRelationEvents() {
        $this->on(static::$eventNewRecordCreated, [$this, 'onInitGroups']);
        $this->on(static::$eventNewRecordCreated, [$this, 'onInitRemark']);
        $this->on(static::EVENT_AFTER_INSERT, [$this, 'onInsertRelation']);
        $this->on(static::EVENT_AFTER_UPDATE, [$this, 'onUpdateRelation']);
        $this->on(static::EVENT_AFTER_DELETE, [$this, 'onDeleteRelation']);
    }

    protected function createOtherRelation($config = []) {
        $self = static::className();
        return new $self([$config]);
    }

    /**
     * Build a suspend relation.
     * This method may involve more DB operations, I strongly recommend this method
     * to be placed in transaction execution, in order to ensure data consistency.
     * @param type $user Initiator
     * @param type $other Recipient
     */
    public static function buildSuspendRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeSuspend;
        $relation->confirmation = false;
        return $relation;
    }

    /**
     * Build a normal relation.
     * This method may involve more DB operations, I strongly recommend this method
     * to be placed in transaction execution, in order to ensure data consistency.
     * @param type $user Initiator
     * @param type $other Recipient
     */
    public static function buildNormalRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeNormal;
        $relation->confirmation = true;
        return $relation;
    }

    /**
     * Build a banned relation.
     * This method may involve more DB operations, I strongly recommend this method
     * to be placed in transaction execution, in order to ensure data consistency.
     * @param type $user Initiator
     * @param type $other Recipient
     */
    public static function buildBannedRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeNormal;
        $relation->confirmation = false;
        return $relation;
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return \static
     */
    protected static function buildRelation($user, $other) {
        return static::buildRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return \static
     */
    protected static function buildRelationByUserGuid($user_guid, $other_guid) {
        $r = static::buildNoInitModel();
        $createdByAttribute = $r->createdByAttribute;
        $otherGuidAttribute = $r->otherGuidAttribute;
        $relation = static::findOne([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
        if (!$relation) {
            $relation = new static([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
        }
        return $relation;
    }

    /**
     * 
     * @param type $relation
     * @return \static
     */
    protected static function buildOppositeRelation($relation) {
        $createdByAttribute = $relation->createdByAttribute;
        $otherGuidAttribute = $relation->otherGuidAttribute;
        $relationTypeAttribute = $relation->relationTypeAttribute;
        $opposite = static::buildRelationByUserGuid($relation->$otherGuidAttribute, $relation->$createdByAttribute);
        $opposite->confirmation = $relation->confirmation;
        $opposite->$relationTypeAttribute = $relation->$relationTypeAttribute;
        return $opposite;
    }

    /**
     * 
     * @return integer|false The number of relations removed, or false if the remove
     * is unsuccessful for some reason. Note that it is possible the number of relations
     * removed is 0, even though the remove execution is successful.
     */
    public function remove() {
        return $this->delete();
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return type
     */
    public static function removeOneRelation($user, $other) {
        return static::removeOneRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return type
     */
    public static function removeOneRelationByUserGuid($user_guid, $other_guid) {
        $r = static::buildNoInitModel();
        $createdByAttribute = $r->createdByAttribute;
        $otherGuidAttribute = $r->otherGuidAttribute;
        $relation = static::findOne([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
        return $relation->delete();
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return integer The number of relations removed.
     */
    public static function removeAllRelations($user, $other) {
        return static::removeAllRelationsByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return integer The number of relations removed.
     */
    public static function removeAllRelationsByUserGuid($user_guid, $other_guid) {
        $r = static::buildNoInitModel();
        $createdByAttribute = $r->createdByAttribute;
        $otherGuidAttribute = $r->otherGuidAttribute;
        return static::deleteAll([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return type
     */
    public static function findOneRelation($user, $other) {
        return static::findOneRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return type
     */
    public static function findOneOppositeRelation($user, $other) {
        return static::findOneRelationByUserGuid($other->guid, $user->guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return type
     */
    public static function findOneOppositeRelationByUserGuid($user_guid, $other_guid) {
        return static::findOneRelationByUserGuid($other_guid, $user_guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return type
     */
    public static function findOneRelationByUserGuid($user_guid, $other_guid) {
        $r = static::buildNoInitModel();
        $createdByAttribute = $r->createdByAttribute;
        $otherGuidAttribute = $r->otherGuidAttribute;
        return static::findOne([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
    }

    /**
     * 
     * @param type $user
     * @param type $other
     * @return type
     */
    public static function findAllRelations($user, $other) {
        return static::findAllRelationsByUserGuid($user->guid, $other->guid);
    }

    public static function findAllOppositeRelations($user, $other) {
        return static::findAllRelationsByUserGuid($other->guid, $user->guid);
    }

    public static function findAllOppositeRelationsByUserGuid($user_guid, $other_guid) {
        return static::findAllRelationsByUserGuid($other_guid, $user_guid);
    }

    /**
     * 
     * @param string $user_guid
     * @param string $other_guid
     * @return type
     */
    public static function findAllRelationsByUserGuid($user_guid, $other_guid) {
        $r = static::buildNoInitModel();
        $createdByAttribute = $r->createdByAttribute;
        $otherGuidAttribute = $r->otherGuidAttribute;
        return static::findAll([$createdByAttribute => $user_guid, $otherGuidAttribute => $other_guid]);
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onInsertRelation($event) {
        $sender = $event->sender;
        $opposite = static::buildOppositeRelation($sender);
        $opposite->off(static::EVENT_AFTER_INSERT, [$opposite, 'onInsertRelation']);
        $opposite->save();
        $opposite->on(static::EVENT_AFTER_INSERT, [$opposite, 'onInsertRelation']);
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onUpdateRelation($event) {
        $sender = $event->sender;
        $opposite = static::buildOppositeRelation($sender);
        $opposite->off(static::EVENT_AFTER_UPDATE, [$opposite, 'onUpdateRelation']);
        $opposite->save();
        $opposite->on(static::EVENT_AFTER_UPDATE, [$opposite, 'onUpdateRelation']);
    }

    /**
     * 
     * @param \yii\base\Event $event
     */
    public function onDeleteRelation($event) {
        $sender = $event->sender;
        $createdByAttribute = $sender->createdByAttribute;
        $otherGuidAttribute = $sender->otherGuidAttribute;
        $sender->off(static::EVENT_AFTER_DELETE, [$sender, 'onDeleteRelation']);
        static::removeAllRelationsByUserGuid($sender->$otherGuidAttribute, $sender->$createdByAttribute);
        $sender->on(static::EVENT_AFTER_DELETE, [$sender, 'onDeleteRelation']);
    }

}
