<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace vistart\Models\traits;

/**
 * Description of MutualTrait
 *
 * @author vistart <i@vistart.name>
 */
trait MutualTrait
{

    public $otherGuidAttribute = 'other_guid';

    /**
     * Get initiator.
     * @return \vistart\Models\queries\BaseUserQuery
     */
    public function getInitiator()
    {
        return $this->getUser();
    }

    /**
     * Get recipient.
     * @return \vistart\Models\queries\BaseUserQuery
     */
    public function getRecipient()
    {
        if (!is_string($this->otherGuidAttribute)) {
            return null;
        }
        $userClass = $this->userClass;
        $model = $userClass::buildNoInitModel();
        return $this->hasOne($userClass::className(), [$model->guidAttribute => $this->otherGuidAttribute]);
    }

    public function setRecipient($user)
    {
        if (!is_string($this->otherGuidAttribute)) {
            return null;
        }
        $otherGuidAttribute = $this->otherGuidAttribute;
        return $this->$otherGuidAttribute = $user->guid;
    }

    public function getMutualRules()
    {
        $rules = [];
        if (is_string($this->otherGuidAttribute)) {
            $rules = [
                [$this->otherGuidAttribute, 'required'],
                [$this->otherGuidAttribute, 'string', 'max' => 36],
            ];
        }
        return $rules;
    }
}
