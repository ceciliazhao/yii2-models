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
    //public $remarkAttribute = 'remark';
    
    /**
     * @var string the attribute name of which determines the `favorite` field.
     */
    public $favoriteAttribute = 'favorite';
    public static $relationTypeNormal = 0x00;
    public static $relationTypeSuspend = 0x01;
    public static $relationTypeBanned = 0x10;
    public $relationTypes = [
        0x00 => 'Normal',
        0x01 => 'Suspend',
        0x10 => 'Banned',
    ];

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
    }

}
