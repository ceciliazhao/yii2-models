<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\queries;

/**
 * Description of BaseUserRelationQuery
 *
 * Note: You must specify $modelClass property, and the class must be the subclass
 * of `\vistart\Models\models\BaseUserRelationModel`.
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationQuery extends BaseEntityQuery {

    public function opposite($user, $other, $db = null) {
        $modelClass = $this->modelClass;
        $model = $modelClass::buildNoInitModel();
        return $this->andWhere([$model->createdByAttribute => $other->guid, $model->otherGuidAttribute => $user->guid])->one($db);
    }

    public function opposites($user, $otherGuids, $db = null) {
        $modelClass = $this->modelClass;
        $model = $modelClass::buildNoInitModel();
        return $this->andWhere([$model->createdByAttribute => $otherGuids, $model->otherGuidAttribute => $user->guid])->all($db);
    }

}
