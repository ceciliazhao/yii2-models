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
 * Relation features.
 * Note: Several methods associated with "inserting", "updating" and "removing" may
 * involve more DB operations, I strongly recommend those methods to be placed in
 * transaction execution, in order to ensure data consistency.
 * @property array $groupGuids the guid array of all groups which owned by current relation.
 * @property boolean $isFavorite
 * @property-read \vistart\Models\models\BaseUserRelationModel $opposite
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserRelationTrait {

    /**
     * @var string the another party of the relation.
     */
    public $otherGuidAttribute = 'other_guid';

    /**
     * @var string the attribute name of which determines the `groups` field.
     * you can assign it to `false` if you want to disable group features.
     */
    public $groupsAttribute = 'groups';
    
    /**
     * @var integer 
     */
    public $groupLimited = 10;

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

    /**
     * 
     * @return boolean
     */
    public function getIsFavorite() {
        $favoriteAttribute = $this->favoriteAttribute;
        return (int) $this->$favoriteAttribute > 0;
    }

    /**
     * 
     * @param boolean $fav
     */
    public function setIsFavorite($fav) {
        $favoriteAttribute = $this->favoriteAttribute;
        $this->$favoriteAttribute = ($fav ? 1 : 0);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return array_merge(parent::rules(), $this->getUserRelationRules());
    }

    /**
     * Validation rules associated with user relation.
     * @return array
     */
    public function getUserRelationRules() {
        return array_merge([
            [[$this->relationTypeAttribute], 'boolean'],
            [[$this->relationTypeAttribute], 'default', 'value' => static::$relationTypeNormal],
                ], $this->getRemarkRules(), $this->getFavoriteRules(), $this->getGroupsRules(), $this->getOtherGuidRules());
    }

    /**
     * Validation rules associated with remark attribute.
     * @return array rules.
     */
    public function getRemarkRules() {
        return is_string($this->remarkAttribute) ? [
            [[$this->remarkAttribute], 'string'],
            [[$this->remarkAttribute], 'default', 'value' => ''],
                ] : [];
    }

    /**
     * Validation rules associated with favorites attribute.
     * @return array
     */
    public function getFavoriteRules() {
        return is_string($this->favoriteAttribute) ? [
            [[$this->favoriteAttribute], 'boolean'],
            [[$this->favoriteAttribute], 'default', 'value' => 0],
                ] : [];
    }

    /**
     * Validation rules associated with groups attribute.
     * @return array
     */
    public function getGroupsRules() {
        return is_string($this->groupsAttribute) ? [
            [[$this->groupsAttribute], 'required'],
            [[$this->groupsAttribute], 'string'],
            [[$this->groupsAttribute], 'default', 'value' => '[]'],
                ] : [];
    }

    /**
     * Validation rules associated with other guid attribute.
     * @return array
     */
    public function getOtherGuidRules() {
        $rules = [
            [[$this->otherGuidAttribute, $this->relationTypeAttribute], 'required'],
            [[$this->otherGuidAttribute], 'string', 'max' => 36],
            [[$this->otherGuidAttribute, $this->createdByAttribute], 'unique', 'targetAttribute' => [$this->otherGuidAttribute, $this->createdByAttribute]],
        ];
        return $rules;
    }

    /**
     * 
     * @return array
     */
    public function getGroupGuids($checkValid = false) {
        $groupsAttribute = $this->groupsAttribute;
        if ($this->groupsAttribute === false) {
            return [];
        }
        $jsonParser = new \yii\web\JsonParser();
        $guids = $jsonParser->parse($this->$groupsAttribute, true);
        if ($checkValid) {
            $checkedGuids = $this->unsetInvalidGroups($guids);
            if (!empty(array_diff($guids, $checkedGuids))) {
                $guids = $this->setGroupGuids($checkedGuids, false);
            }
        }
        return $guids;
    }

    /**
     * Set the group guids, it may check all guids if they are valid.
     * @param array $guids
     * @param boolean $checkValid determines whether check the group is valid.
     * @return string array of all guids valid in json format.
     */
    public function setGroupGuids($guids = [], $checkValid = true) {
        if (!is_array($guids) || $this->groupsAttribute === false) {
            return null;
        }
        if ($checkValid) {
            $guids = $this->unsetInvalidGroups($guids);
        }
        $groupsAttribute = $this->groupsAttribute;
        return $this->$groupsAttribute = json_encode(array_values($guids));
    }
    
    /**
     * Remove invalid group guid from provided guid array.
     * @param array $guids
     * @return array
     */
    protected function unsetInvalidGroups($guids) {
        $groupClass = $this->groupClass;
        $guids = \vistart\Helpers\Number::unsetInvalidUuids($guids);
        foreach ($guids as $key => $guid) {
            $group = $groupClass::findOne($guid);
            if (!$group) {
                unset($guids[$key]);
            }
        }
        return $guids;
    }

    /**
     * Get group which guid is `$guid`.
     * @param string $guid
     * @return \vistart\Models\models\BaseUserRelationGroupModel
     */
    public function getGroup($guid) {
        if (empty($this->groupClass) || !is_string($this->groupClass) || $this->groupsAttribute === false) {
            return null;
        }
        $groupClass = $this->groupClass;
        return $groupClass::findOne($guid);
    }

    /**
     * Get all relations in specified group whose guid is `$guid`.
     * @param string $guid the guid of group.
     * @return array all relations in specified group.
     */
    public function getGroupMembers($guid) {
        $group = $this->getGroup($guid);
        if (empty($group)) {
            return null;
        }
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute])
                        ->andWhere(['like', $this->groupsAttribute, $guid])->all();
    }
    
    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @return array
     */
    public static function findOnesAllRelations($user) {
        return static::findOnesAllRelationsByUserGuid($user->guid);
    }
    
    /**
     * 
     * @param srting $userGuid
     * @return array
     */
    public static function findOnesAllRelationsByUserGuid($userGuid) {
        return static::findOne([$this->createdByAttribute => $userGuid]);
    }

    /**
     * 
     * @return array
     */
    public function getAllGroups() {
        if (empty($this->groupClass) || !is_string($this->groupClass) || $this->groupsAttribute === false) {
            return null;
        }
        $groupClass = $this->groupClass;
        $createdByAttribute = $this->createdByAttribute;
        return $groupClass::findAll([$createdByAttribute => $this->$createdByAttribute]);
    }

    /**
     * 
     * @return array
     */
    public function getNonGroupMembers() {
        $createdByAttribute = $this->createdByAttribute;
        return static::find()->where([$createdByAttribute => $this->$createdByAttribute, $this->groupsAttribute => json_encode([])])->all();
    }

    /**
     * Add group specified by `$guid`.
     * @param string $guid the guid of group to be added.
     * @return false|array
     */
    public function addGroupByGuid($guid) {
        if ($this->groupsAttribute === false) {
            return false;
        }
        $groupGuids = $this->getGroupGuids();
        if (array_search($guid, $groupGuids) === false) {
            $groupGuids[] = $guid;
            $this->setGroupGuids($groupGuids);
        }
        return $this->getGroupGuids();
    }

    /**
     * Add group.
     * @param \vistart\Models\models\BaseUserRelationGroupModel $group the group to be added.
     * @return false|array
     */
    public function addGroup($group) {
        return $this->addGroupByGuid($group->guid);
    }

    /**
     * Remove group specified by `$guid`.
     * @param string $guid the guid of group to be removed.
     */
    public function removeGroup($guid) {
        if ($this->groupsAttribute === false) {
            return false;
        }
        $groupGuids = $this->getGroupGuids();
        if (($key = array_search($guid, $groupGuids)) !== false) {
            unset($groupGuids[$key]);
            $this->setGroupGuids($groupGuids);
        }
        return $this->getGroupGuids();
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

    /**
     * 
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    public function getOpposite() {
        if ($this->isNewRecord) {
            return null;
        }
        $createdByAttribute = $this->createdByAttribute;
        $otherGuidAttribute = $this->otherGuidAttribute;
        return static::findOneRelationByUserGuid($this->$otherGuidAttribute, $this->$createdByAttribute);
    }

    /**
     * Build a suspend relation.
     * @param type $user Initiator
     * @param type $other Recipient
     * @return \vistart\Models\models\BaseUserRelationModel The relation will be
     * given if exists, or return a new relation.
     */
    public static function buildSuspendRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeSuspend;
        return $relation;
    }

    /**
     * Build a normal relation.
     * @param type $user Initiator
     * @param type $other Recipient
     * @return \vistart\Models\models\BaseUserRelationModel The relation will be
     * given if exists, or return a new relation.
     */
    public static function buildNormalRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeNormal;
        return $relation;
    }

    /**
     * Build a banned relation.
     * @param type $user Initiator
     * @param type $other Recipient
     * @return \vistart\Models\models\BaseUserRelationModel The relation will be
     * given if exists, or return a new relation.
     */
    public static function buildBannedRelation($user, $other) {
        $relation = static::buildRelation($user, $other);
        $relationTypeAttribute = $r->relationTypeAttribute;
        $relation->$relationTypeAttribute = self::$relationTypeNormal;
        return $relation;
    }

    /**
     * Build relation between initiator and recipient.
     * @see buildRelationByUserGuid
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return \vistart\Models\models\BaseUserRelationModel The relation will be
     * given if exists, or return a new relation.
     */
    protected static function buildRelation($user, $other) {
        return static::buildRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * Build relation between initiator whose guid is $userGuid and recipient
     * whose guid is $otherGuid. 
     * @param string $userGuid
     * @param string $otherGuid
     * @return \vistart\Models\models\BaseUserRelationModel The relation will be
     * given if exists, or return a new relation.
     */
    protected static function buildRelationByUserGuid($userGuid, $otherGuid) {
        $rni = static::buildNoInitModel();
        $createdByAttribute = $rni->createdByAttribute;
        $otherGuidAttribute = $rni->otherGuidAttribute;
        $relation = static::findOne([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
        if (!$relation) {
            $relation = new static([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
        }
        return $relation;
    }

    /**
     * Build opposite relation throughout the current relation. The opposite
     * relation will be given if existed.
     * @param type $relation
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    protected static function buildOppositeRelation($relation) {
        $createdByAttribute = $relation->createdByAttribute;
        $otherGuidAttribute = $relation->otherGuidAttribute;
        $relationTypeAttribute = $relation->relationTypeAttribute;
        $opposite = static::buildRelationByUserGuid($relation->$otherGuidAttribute, $relation->$createdByAttribute);
        $opposite->$relationTypeAttribute = $relation->$relationTypeAttribute;
        return $opposite;
    }

    /**
     * Remove myself.
     * @return integer|false The number of relations removed, or false if the remove
     * is unsuccessful for some reason. Note that it is possible the number of relations
     * removed is 0, even though the remove execution is successful.
     */
    public function remove() {
        return $this->delete();
    }

    /**
     * Remove first relation between initiator and recipient.
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return integer|false
     */
    public static function removeOneRelation($user, $other) {
        return static::removeOneRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * Remove first relation between initiator whose guid is $userGuid and
     * recipient whose $guid is $otherGuid.
     * @param string $userGuid Initiator's guid.
     * @param string $otherGuid Recipient's guid.
     * @return integer|false The number of relations removed, or false if the remove
     * is unsuccessful for some reason. Note that it is possible the number of relations
     * removed is 0, even though the remove execution is successful.
     */
    public static function removeOneRelationByUserGuid($userGuid, $otherGuid) {
        $rni = static::buildNoInitModel();
        $createdByAttribute = $rni->createdByAttribute;
        $otherGuidAttribute = $rni->otherGuidAttribute;
        $relation = static::findOne([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
        return $relation->delete();
    }

    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return integer The number of relations removed.
     */
    public static function removeAllRelations($user, $other) {
        return static::removeAllRelationsByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param string $userGuid
     * @param string $otherGuid
     * @return integer The number of relations removed.
     */
    public static function removeAllRelationsByUserGuid($userGuid, $otherGuid) {
        $rni = static::buildNoInitModel();
        $createdByAttribute = $rni->createdByAttribute;
        $otherGuidAttribute = $rni->otherGuidAttribute;
        return static::deleteAll([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
    }

    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    public static function findOneRelation($user, $other) {
        return static::findOneRelationByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    public static function findOneOppositeRelation($user, $other) {
        return static::findOneRelationByUserGuid($other->guid, $user->guid);
    }

    /**
     * 
     * @param string $userGuid
     * @param string $otherGuid
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    public static function findOneOppositeRelationByUserGuid($userGuid, $otherGuid) {
        return static::findOneRelationByUserGuid($otherGuid, $userGuid);
    }

    /**
     * 
     * @param string $userGuid
     * @param string $otherGuid
     * @return \vistart\Models\models\BaseUserRelationModel
     */
    public static function findOneRelationByUserGuid($userGuid, $otherGuid) {
        $rni = static::buildNoInitModel();
        $createdByAttribute = $rni->createdByAttribute;
        $otherGuidAttribute = $rni->otherGuidAttribute;
        return static::findOne([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
    }

    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return array
     */
    public static function findAllRelations($user, $other) {
        return static::findAllRelationsByUserGuid($user->guid, $other->guid);
    }

    /**
     * 
     * @param \vistart\Models\models\BaseUserModel $user
     * @param \vistart\Models\models\BaseUserModel $other
     * @return array
     */
    public static function findAllOppositeRelations($user, $other) {
        return static::findAllRelationsByUserGuid($other->guid, $user->guid);
    }

    /**
     * 
     * @param string $userGuid
     * @param string $otherGuid
     * @return array
     */
    public static function findAllOppositeRelationsByUserGuid($userGuid, $otherGuid) {
        return static::findAllRelationsByUserGuid($otherGuid, $userGuid);
    }

    /**
     * 
     * @param string $userGuid
     * @param string $otherGuid
     * @return array
     */
    public static function findAllRelationsByUserGuid($userGuid, $otherGuid) {
        $rni = static::buildNoInitModel();
        $createdByAttribute = $rni->createdByAttribute;
        $otherGuidAttribute = $rni->otherGuidAttribute;
        return static::findAll([$createdByAttribute => $userGuid, $otherGuidAttribute => $otherGuid]);
    }

    /**
     * The event triggered after insert new relation.
     * The opposite relation should be inserted without triggering events
     * simultaneously after new relation inserted,
     * @param \yii\base\Event $event
     */
    public function onInsertRelation($event) {
        $sender = $event->sender;
        $opposite = static::buildOppositeRelation($sender);
        $opposite->off(static::EVENT_AFTER_INSERT, [$opposite, 'onInsertRelation']);
        $result = $opposite->save();
        if (!$result) {
            $this->recordWarnings();
        }
        $opposite->on(static::EVENT_AFTER_INSERT, [$opposite, 'onInsertRelation']);
    }

    /**
     * The event triggered after update relation.
     * The opposite relation should be updated without triggering events
     * simultaneously after existed relation removed.
     * @param \yii\base\Event $event
     */
    public function onUpdateRelation($event) {
        $sender = $event->sender;
        $opposite = static::buildOppositeRelation($sender);
        $opposite->off(static::EVENT_AFTER_UPDATE, [$opposite, 'onUpdateRelation']);
        $result = $opposite->save();
        if (!$result) {
            $this->recordWarnings();
        }
        $opposite->on(static::EVENT_AFTER_UPDATE, [$opposite, 'onUpdateRelation']);
    }

    /**
     * The event triggered after delete relation.
     * The opposite relation should be deleted without triggering events
     * simultaneously after existed relation removed.
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
