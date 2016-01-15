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
 * $contentAttribute 关系组名称。
 * $contentTypeAttribute 关系组类型。
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait UserRelationGroupTrait {

    public $relationClass;
    
    /**
     * add a group to user.
     * @param \vistart\Models\models\BaseUserModel $user
     */
    public static function add($user) {
        $group = $user->createModel(static::className());
        return $group->save();
    }
    
    /**
     * 
     */
    public function initUserRelationGroupEvents() {
        $this->on(static::EVENT_BEFORE_DELETE, [$this, 'onDeleteGroup']);
    }
    
    /**
     * the event triggered before deleting group.
     * I do not remove group's guid from groupsAttribute which contains the guid
     * of group to be deleted.
     * @param \yii\base\Event $event
     */
    public function onDeleteGroup($event) {
        /*
        $relationClass = $this->relationClass;
        if (!is_string($relationClass)) {
            throw new \yii\base\NotSupportedException('You must specify the name of relation class.');
        }
        $sender = $event->sender;
        $groupGuid = $sender->guid;
        $createdByAttribute = $sender->createdByAttribute;
        $relations = $relationClass::findOnesAllRelations($sender->$createdByAttribute);
        foreach ($relations as $relation) {
            $relation->removeGroup($groupGuid);
            $relation->save();
        }
         */
    }
}
