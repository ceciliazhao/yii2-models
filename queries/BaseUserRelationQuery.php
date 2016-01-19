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

use vistart\Models\models\BaseUserModel;
use vistart\Models\models\BaseUserRelationModel;
use yii\db\Connection;

/**
 * Description of BaseUserRelationQuery
 *
 * Note: You must specify $modelClass property, and the class must be the subclass
 * of `\vistart\Models\models\BaseUserRelationModel`.
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationQuery extends BaseEntityQuery {

    /**
     * Get the opposite one.
     * @param BaseUserModel $user
     * @param BaseUserModel $other
     * @param Connection $db
     * @return BaseUserRelationModel
     */
    public function opposite($user, $other, $db = null) {
        $model = $this->noInitModel;
        return $this->andWhere([$model->createdByAttribute => $other->guid, $model->otherGuidAttribute => $user->guid])->one($db);
    }

    /**
     * Get all the opposites.
     * @param BaseUserModel $user
     * @param array $otherGuids
     * @param Connection $db
     * @return BaseUserRelationModel
     */
    public function opposites($user, $otherGuids, $db = null) {
        if (!is_array($otherGuids) || empty($otherGuids)) {
            return null;
        }
        $model = $this->noInitModel;
        return $this->andWhere([$model->createdByAttribute => array_values($otherGuids), $model->otherGuidAttribute => $user->guid])->all($db);
    }

}
