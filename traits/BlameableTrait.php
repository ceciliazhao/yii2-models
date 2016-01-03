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

use yii\behaviors\BlameableBehavior;

/**
 * 
 * @property array $blameableRules Get or set all the rules associated with
 * creator, updater, content and its ID, as well as all the inherited rules.
 * @property array $blameableBehaviors Get or set all the behaviors assoriated
 * with creator and updater, as well as all the inherited behaviors. 
 * @property-read mixed $content
 * @property-read array $contentTypes
 * @property-read boolean $contentCanBeEdited
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait BlameableTrait {

    use ConfirmationTrait;

    private $_blameableRules = [];
    private $_blameableBehaviors = [];

    /**
     * @var boolean|string|array Specify the attribute(s) name of content(s). If
     * there is only one content attribute, you can assign its name. Or there
     * is multiple attributes associated with contents, you can assign their
     * names in array. If you don't want to use this feature, please assign
     * false.
     * 
     * For example:
     * ```php
     * public $contentAttribute = 'comment'; // only one field named as 'comment'.
     * ```
     * or
     * ```php
     * public $contentAttribute = ['year', 'month', 'day']; // multiple fields.
     * ```
     * or
     * ```php
     * public $contentAttribute = false; // no need of this feature.
     * ```
     * 
     * If you don't need this feature, you should add rules corresponding with
     * `content` in `rules()` method of your user model by yourself.
     */
    public $contentAttribute = false;

    /**
     * @var array built-in validator name or validatation method name and
     * additional parameters.
     */
    public $contentAttributeRule = null;

    /**
     * @var boolean|array Specify the logic type of content, not data type. If
     * your content doesn't need this feature. please specify false. If the
     * $contentAttribute is specified to false, this attribute will be skipped.
     * 
     * ```php
     * public $contentTypeAttribute = [
     *     'public',
     *     'private',
     *     'friend',
     * ];
     * ```
     */
    public $contentTypeAttribute = false;

    /**
     * @var string the attribute that specify the name of id of 
     * Yii::$app->user->identity. Or same as $createByAttribute.
     */
    public $identityGuidAttribute = 'user_guid';

    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the creator ID.
     */
    public $createdByAttribute = "user_guid";

    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updatedByAttribute = "user_guid";

    /**
     * @inheritdoc
     */
    public function rules() {
        return $this->blameableRules;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return $this->blameableBehaviors;
    }

    /**
     * 
     * @return mixed
     */
    public function getContent() {
        $contentAttribute = $this->contentAttribute;
        if ($this->contentAttribute === false)
            return null;
        if (is_array($contentAttribute)) {
            $content = "";
            foreach ($contentAttribute as $attribute) {
                $content .= $this->$attribute;
            }
            return $content;
        }
        return $this->$contentAttribute;
    }

    /**
     * 
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    public function getContentTypes() {
        if ($this->contentAttribute === false)
            return null;
        throw new \yii\base\NotSupportedException("This method is not implemented.");
    }

    /**
     * 
     * @return boolean
     * @throws \yii\base\NotSupportedException
     */
    public function getContentCanBeEdited() {
        if ($this->contentAttribute === false)
            return false;
        throw new \yii\base\NotSupportedException("This method is not implemented.");
    }

    /**
     * 
     * @return array
     */
    public function getBlameableRules() {
        if (empty($this->_blameableRules) || !is_array($this->_blameableRules)) {
            $this->_blameableRules = array_merge(
                    parent::rules(), $this->confirmationRules, [
                [[$this->createdByAttribute], 'safe'],
                [[$this->createdByAttribute], 'string', 'max' => 36],
                [[$this->createdByAttribute, $this->idAttribute], 'unique', 'targetAttribute' =>
                    [$this->createdByAttribute, $this->idAttribute],
                ],
                    ]
            );
            if ($this->contentAttribute) {
                $this->_blameableRules[] = [
                    [$this->contentAttribute], 'required'
                ];
                if ($this->contentAttributeRule) {
                    $this->_blameableRules[] = array_merge(
                        [[$this->contentAttribute]], $this->contentAttributeRule
                    );
                }
            }
        }
        return $this->_blameableRules;
    }

    /**
     * 
     * @param array $rules
     */
    public function setBlameableRules($rules = []) {
        $this->_blameableRules = $rules;
    }

    /**
     * 
     * @return array
     */
    public function getBlameableBehaviors() {
        if (empty($this->_blameableBehaviors) || !is_array($this->_blameableBehaviors)) {
            $behaviors = parent::behaviors();
            $behaviors[] = [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => $this->createdByAttribute,
                'updatedByAttribute' => $this->updatedByAttribute,
                'value' => [$this, 'onGetCurrentUserGuid'],
            ];
            $this->_blameableBehaviors = $behaviors;
        }
        return $this->_blameableBehaviors;
    }

    /**
     * 
     * @param array $behaviors
     */
    public function setBlameableBehaviors($behaviors = []) {
        $this->_blameableBehaviors = $behaviors;
    }

    /**
     * This event is triggered before the model update.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onContentChanged($event) {
        $sender = $event->sender;
        $sender->resetConfirmation();
    }

    /**
     * Return the current user's GUID if current model doesn't specify the owner
     * yet, or return the owner's GUID if current model has been specified.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @return string the GUID of current user or the owner.
     */
    public function onGetCurrentUserGuid($event) {
        $sender = $event->sender;
        if (!is_string($sender->identityGuidAttribute)) {
            $sender->identityGuidAttribute = $sender->createdByAttribute;
        }
        $identityGuidAttribute = $sender->identityGuidAttribute;
        if (isset($sender->$identityGuidAttribute)) {
            return $sender->$identityGuidAttribute;
        }
        $identity = \Yii::$app->user->identity;
        if (!$identity) {
            return null;
        }
        $identityGuidAttribute = $identity->guidAttribute;
        return $identity->$identityGuidAttribute;
    }

}
