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
class BaseUserRelationQuery extends BaseBlameableQuery
{

    /**
     * Get the opposite relation.
     * @param BaseUserModel|string $user initiator
     * @param BaseUserModel|string $other recipient.
     * @param Connection $database
     * @return BaseUserRelationModel
     */
    public function opposite($user, $other, $database = null)
    {
        $model = $this->noInitModel;
        return $this->andWhere([$model->createdByAttribute => $other, $model->otherGuidAttribute => $user])->one($database);
    }

    /**
     * Get all the opposites.
     * @param string $user initator.
     * @param array $others all recipients.
     * @param Connection $database
     * @return array array of BaseUserRelationModel instances.
     */
    public function opposites($user, $others = [], $database = null)
    {
        $model = $this->noInitModel;
        $query = $this->andWhere([$model->otherGuidAttribute => $user]);
        if (!empty($others))
        {
            $query = $query->andWhere([$model->createdByAttribute => array_values($others)]);
        }
        return $query->all($database);
    }

    /**
     * Specify initiators.
     * @param string|array $users the guid of initiator if string, or guid array
     * of initiators if array.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function initiators($users = [])
    {
        if (empty($users))
        {
            return $this;
        }
        $model = $this->noInitModel;
        return $this->andWhere([$model->createdByAttribute => $users]);
    }

    /**
     * Specify recipients.
     * @param string|array $users the guid of recipient if string, or guid array
     * of recipients if array.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function recipients($users = [])
    {
        if (empty($users))
        {
            return $this;
        }
        $model = $this->noInitModel;
        return $this->andWhere([$model->otherGuidAttribute => $users]);
    }

    /**
     * Specify groups.
     * This method will be skipped if not enable the group features (`$multiBlamesAttribute = false`).
     * @param string|array $groups the guid of group If string, or guid array of
     * groups if array. If you want to get ungrouped relation(s), please assign
     * empty array, or if you do not want to delimit group(s), please do not
     * access this method, or assign null.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function groups($groups = [])
    {
        if ($groups === null)
        {
            return $this;
        }
        $model = $this->noInitModel;
        if (!is_string($model->multiBlamesAttribute))
        {
            return $this;
        }
        if (empty($groups))
        {
            return $this->andWhere([$model->multiBlamesAttribute => BaseUserRelationModel::getEmptyGroupJson()]);
        }
        return $this->andWhere(['or like', $model->multiBlamesAttribute, $groups]);
    }
}
