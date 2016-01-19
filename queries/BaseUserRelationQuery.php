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
class BaseUserRelationQuery extends BaseEntityQuery
{

    /**
     * Get the opposite relation.
     * @param BaseUserModel|string $user initiator
     * @param BaseUserModel|string $other recipient.
     * @param Connection $db
     * @return BaseUserRelationModel
     */
    public function opposite($user, $other, $db = null)
    {
        $model = $this->noInitModel;
        $user_guid = '';
        $other_guid = '';
        if (is_string($user)) {
            $user_guid = $user;
        }
        if ($user instanceof BaseUserModel) {
            $user_guid = $user->guid;
        }
        if (is_string($other)) {
            $other_guid = $other;
        }
        if ($other instanceof BaseUserModel) {
            $other_guid = $other->guid;
        }
        return $this->andWhere([$model->createdByAttribute => $other_guid, $model->otherGuidAttribute => $user_guid])->one($db);
    }

    /**
     * Get all the opposites.
     * @param string $user initator.
     * @param array $others all recipients.
     * @param Connection $db
     * @return array array of BaseUserRelationModel instances.
     */
    public function opposites($user, $others = [], $db = null)
    {
        $user_guid = '';
        if (is_string($user)) {
            $user_guid = $user;
        }
        if ($user instanceof BaseUserModel) {
            $user_guid = $user->guid;
        }
        $others = (array) $others;
        foreach ($others as $key => $other) {
            if ($other instanceof BaseUserModel) {
                $other = $other->guid;
            }
            if (!is_string($other)) {
                unset($others[$key]);
            }
        }
        $model = $this->noInitModel;
        $query = $this->andWhere([$model->otherGuidAttribute => $user_guid]);
        if (!empty($otherGuids)) {
            $query = $query->andWhere([$model->createdByAttribute => array_values($others)]);
        }
        return $query->all($db);
    }

    /**
     * Specify initiators.
     * @param string|array $guids the guid of initiator if string, or guid array
     * of initiators if array.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function initiators($guids = [])
    {
        $guids = (array) $guids;
        if (empty($guids)) {
            return $this;
        }
        $model = $this->noInitModel;
        return $this->andWhere([$model->createdByAttribute => $guids]);
    }

    /**
     * Specify recipients.
     * @param string|array $guids the guid of recipient if string, or guid array
     * of recipients if array.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function recipients($guids = [])
    {
        $guids = (array) $guids;
        if (empty($guids)) {
            return $this;
        }
        $model = $this->noInitModel;
        return $this->andWhere([$model->otherGuidAttribute => $guids]);
    }

    /**
     * Specify groups.
     * This method will be skipped if not enable the group features (`$multiBlamesAttribute = false`).
     * @param string|array $groups the guid of group If string, or guid array of
     * groups if array. If you want to get ungrouped relation(s), please assign
     * null or empty array, or if you do not want to delimit group(s), please do
     * not access this method.
     * @return \vistart\Models\queries\BaseUserRelationQuery $this
     */
    public function groups($groups = [])
    {
        $model = $this->noInitModel;
        if (!is_string($model->multiBlamesAttribute)) {
            return $this;
        }
        $groups = (array) $groups;
        if (empty($groups)) {
            return $this->andWhere([$model->multiBlamesAttribute => BaseUserRelationModel::getEmptyBlamesJson()]);
        }
        return $this->andWhere(['or like', $model->multiBlamesAttribute, array_values($groups)]);
    }
}
