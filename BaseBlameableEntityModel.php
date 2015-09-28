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
 * For example:
 * ~~~php
 * * @property string $comment
 * class Example extends BaseBlameableEntityModel
 * {
 *     public static function tableName()
 *     {
 *         return <table_name>;
 *     }
 * 
 *     public function rules()
 *     {
 *         $rules = [
 *             [['comment'], 'required'],
 *             [['comment'], 'string', 'max' => 140], 
 *         ];
 *         return array_merge(parent::rules(), $rules);
 *     }
 * 
 *     public function behaviors()
 *     {
 *         $behaviors = <Your Behaviors>;
 *         return array_merge(parent::behaviors(), $behaviors);
 *     }
 * 
 *     public function attributeLabels()
 *     {
 *         return [
 *             ...
 *         ];
 *     }
 * }
 * 
 * Well, when you are signed-in, you can save a new `Example` instance:
 * $example = new Example();
 * $example->comment = 'New Comment.';
 * $example->save();
 * 
 * or update an existing one:
 * $example = Example::find()
 *                   ->where([$this->createdByAttribute => $user_uuid])
 *                   ->one();
 * if ($example)
 * {
 *     $example->comment => 'Updated Comment.';
 *     $example->save();
 * }
 * ~~~
 *
 * @author vistart <i@vistart.name>
 * @since 1.1
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
     * @var boolean|string Determine the type of combination of creator's GUID 
     * and record's ID. If you don't want to combine them, please set it to false.
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
     * @param \yii\base\Event $event
     */    
    protected function onInitBlameRules($event)
    {
        $sender = $event->sender;
        if (empty($sender->createdByAttributeRules) || !is_array($sender->createdByAttributeRules))
        {
            $sender->createdByAttributeRules = [
                [[$sender->createdByAttribute], parent::VALIDATOR_SAFE,],
            ];
        }
        if (empty($sender->updatedByAttributeRules) || !is_array($sender->updatedByAttributeRules))
        {
            $sender->updatedByAttributeRules = [
                [[$sender->updatedByAttribute], parent::VALIDATOR_SAFE,],
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
     * @param \yii\base\Event $event
     * @return string 
     */
    public function onGetCurrentUserUuid($event)
    {
        $sender = $event->sender;
        $identity = \Yii::$app->user->identity;
        $identityUuidAttribute = $sender->identityUuidAttribute;
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
        $rules = [];
        
        if (!empty($this->createdByAttribute))
        {
            $rules = array_merge($rules, $this->createdByAttributeRules);
        }
        
        if (!empty($this->updatedByAttribute))
        {
            $rules = array_merge($rules, $this->updatedByAttributeRules);
        }
        
        if ($this->createdByCombinedWithId && $this->idAttribute && is_string($this->idAttribute))
        {
            $this->idAttributeSafe = true;
            $rules[] = [
                [$this->createdByAttribute, $this->idAttribute], 
                self::VALIDATOR_UNIQUE, 
                'targetAttribute' => [$this->createdByAttribute, $this->idAttribute]
            ];
        }
        return array_merge(parent::rules(), $rules);
    }
}
