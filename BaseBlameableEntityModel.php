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
 * the current user's GUID.
 *
 * @author vistart <i@vistart.name>
 */
class BaseBlameableEntityModel extends BaseEntityModel
{
    /**
     * @var string the attribute that will receive current user's GUID value.
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = 'user_uuid';
    
    /**
     * @var string the attribute that will receive current user's GUID value.
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = 'updater_uuid';
    
    /**
     * @var array the whole validation rules of creator attribute only, except 
     * of combination rules.
     */
    public $createdByAttributeRules = [];
    
    /**
     * @var array the whole validation rules of updater attribute only, except 
     * of combination rules.
     */
    public $updatedByAttributeRules = [];
    
    /**
     * @var string the attribute that specify the name of id of Yii::$app->user->identity.
     */
    public $identityUuidAttribute = 'user_uuid';
    
    const COMBINATION_UNIQUE = 'unqiue';
    
    /**
     * Determine the type of combination of creator's GUID and record's ID.
     * If you don't want to combine them, please set it to false.
     * @var boolean|string 
     */
    public $createdByCombinedWithId = self::COMBINATION_UNIQUE;
    
    public function init()
    {
        $this->on(self::EVENT_INIT, [$this, 'onInitBlameRules']);
        parent::init();
    }
    
    /**
     * This method will automatically assign the safe validator to createdBy,
     * updatedBy attributes when each of them is empty, because the assignment
     * operation is done after validation.
     * This method does not return anything, and DO NOT call it directly.
     */    
    private function onInitBlameRules()
    {
        if (empty($this->createdByAttributeRules) || !is_array($this->createdByAttributeRules))
        {
            $this->createdByAttributeRules = [
                [[$this->createdByAttribute], parent::VALIDATOR_SAFE,],
            ];
        }
        if (empty($this->updatedByAttributeRules) || !is_array($this->updatedByAttributeRules))
        {
            $this->updatedByAttributeRules = [
                [[$this->updatedByAttribute], parent::VALIDATOR_SAFE,],
            ];
        }
    }
    
    /**
     * @inheritdoc
     * ------------
     * # Behaviors of BaseBlameableEntityModel
     * This method will attach the BlameableBehavior to createdByAttribute,
     * updatedByAttribute with value that returned by specified method.
     */
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
     * This method is ONLY used for being triggered by event. DON'T call it 
     * directly.
     * @param type $event
     * @return string 
     */
    public function onGetCurrentUserUuid($event)
    {
        $identity = Yii::$app->user->identity;
        $identityUuidAttribute = $this->identityUuidAttribute;
        return $identity->$identityUuidAttribute;
    }
    
    /**
     * @inheritdoc
     * ------------
     * # Rules of BaseBlameableEntityModel
     * This method will attach the createdBy, updatedBy, createdByCombinedWithId
     * rules, then return it.
     */
    public function rules()
    {
        $rules = parent::rules();
        
        if (!empty($this->createdByAttribute))
        {
            $rules = array_merge($rules, $this->createdByAttributeRules);
        }
        
        if (!empty($this->updatedByAttribute))
        {
            $rules = array_merge($rules, $this->updatedByAttributeRules);
        }
        
        if ($this->createdByCombinedWithId)
        {
            $rules[] = [
                [$this->createdByAttribute, $this->idAttribute], self::VALIDATOR_UNIQUE, 'targetAttribute' => [$this->createdByAttribute, $this->idAttribute]
            ];
        }
        return $rules;
    }
}
