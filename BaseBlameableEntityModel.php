<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models;
use yii\behaviors\BlameableBehavior;
/**
 * BaseBlameableEntityModel automatically fills the specified attributes with 
 * the current user GUID.
 *
 * @author vistart <i@vistart.name>
 */
class BaseBlameableEntityModel extends BaseEntityModel
{
    /**
     * @var string the attribute that will receive current user GUID value
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = 'user_uuid';
    
    /**
     * @var string the attribute that will receive current user GUID value
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = 'updater_uuid';
    
    public $createdByAttributeRule = [];
    
    public $updatedByAttributeRule = [];
    
    /**
     * @var string the attribute that specify the name of id of Yii::$app->user->identity.
     */
    public $identityIdAttribute = 'user_uuid';
    
    protected function initDefaultValues() 
    {
        // if the $this->createdByAttributeRule or $this->updatedByAttributeRule 
        // is empty array, we will assign a safe validator for each. Because the
        // assignment operation is done after validation.
        if (empty($this->createdByAttributeRule) || !is_array($this->createdByAttributeRule))
        {
            $this->createdByAttributeRule = [
                [$this->createdByAttribute], parent::VALIDATOR_SAFE,
            ];
        }
        if (empty($this->updatedByAttributeRule) || !is_array($this->updatedByAttributeRule))
        {
            $this->updatedByAttributeRule = [
                [$this->updatedByAttribute], parent::VALIDATOR_SAFE,
            ];
        }
        parent::initDefaultValues();
    }
    
    public function behaviors() 
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => BlameableBehavior::className(),
            'createdByAttribute' => $this->createdByAttribute,
            'updatedByAttribute' => $this->updatedByAttribute,
            'value' => [$this, 'onGetCurrentUserUuid'],
        ];
        return $behaviors;
    }
    
    /**
     * This method is ONLY used for be triggered by event. DON'T call it directly.
     * @param type $event
     * @return type
     */
    public function onGetCurrentUserUuid($event)
    {
        $identity = Yii::$app->user->identity;
        $identityIdAttribute = $this->identityIdAttribute;
        return $identity->$identityIdAttribute;
    }
    
    public function rules()
    {
        $rules = parent::rules();
        
        if (!empty($this->createdByAttribute))
        {
            $rules[] = $this->createdByAttributeRule;
        }
        
        if (!empty($this->updatedByAttribute))
        {
            $rules[] = $this->updatedByAttributeRule;
        }
        return $rules;
    }
}
