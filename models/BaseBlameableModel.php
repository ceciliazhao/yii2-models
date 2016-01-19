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

namespace vistart\Models\models;

use vistart\Models\traits\BlameableTrait;

/**
 * BaseBlameableEntityModel automatically fills the specified attributes with 
 * the current user's GUID.
 * 
 * For example:<br/>
 * ~~~php
 * * @property string $comment
 * class Comment extends BaseBlameableEntityModel
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
 * Well, when you're signed-in, you can save a new `Example` instance:
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
 *     $example->comment = 'Updated Comment.';
 *     $example->save();
 * }
 * ~~~
 * 
 * @property array createdByAttributeRules the whole validation rules of 
 * creator attribute only, except of combination rules.
 * @property array updatedByAttributeRules the whole validation rules of 
 * creator attribute only, except of combination rules.
 * @since 1.1
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseBlameableModel extends BaseEntityModel
{

    use BlameableTrait;

    /**
     * 
     */
    public function init()
    {
        if ($this->skipInit)
            return;
        $this->initBlameableEvents();
        parent::init();
    }
}
