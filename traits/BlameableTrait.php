<?php
/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
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
trait BlameableTrait
{
    use ConfirmationTrait;
    
    private $_blameableRules = [];
    private $_blameableBehaviors = [];
    
    /**
     * @var boolean|string|array Specify the attribute(s) name of content(s). If
     * there is only one content attribute, you can specify its name. Or there
     * is multiple attributes associated with contents, you can specify their
     * names in array. If you don't want to use this feature, please specify
     * false.
     * 
     * ```php
     * public $contentAttribute = 'comment';
     * ```
     * or
     * ```php
     * public $contentAttribute = ['year', 'month', 'day'];
     * ```
     * or
     * ```php
     * public $contentAttribute = false;
     * ```
     */
    public $contentAttribute = false;
    
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
    public $createdByAttribute = "user_guid";
    public $updatedByAttribute = "user_guid";
    
    /**
     * 
     */
    public function init()
    {
        if ($this->skipInit) return;
        $this->on(self::$EVENT_CONFIRMATION_CHANGED, [$this, "onConfirmationChanged"]);
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, "onInitConfirmation"]);
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->blameableRules;
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return $this->blameableBehaviors;
    }
    
    /**
     * 
     * @return mixed
     */
    public function getContent()
    {
        $contentAttribute = $this->contentAttribute;
        if ($this->contentAttribute === false) return null;
        if (is_array($contentAttribute))
        {
            $content = "";
            foreach ($contentAttribute as $attribute)
            {
                $content .= $this->$attribute;
            }
            return $content;
        }
        return $this->$contentAttribute;
    }
    
    /**
     * 
     * @return type
     * @throws \yii\base\NotSupportedException
     */
    public function getContentTypes()
    {
        if ($this->contentAttribute === false) return null;
        throw new \yii\base\NotSupportedException("This method is not implemented.");
    }
    
    /**
     * 
     * @throws \yii\base\NotSupportedException
     */
    public function getContentCanBeEdited()
    {
        if ($this->contentAttribute === false) return false;
        throw new \yii\base\NotSupportedException("This method is not implemented.");
    }
    
    public function getBlameableRules()
    {
        if (empty($this->_blameableRules) || !is_array($this->_blameableRules))
        {
            $this->_blameableRules = array_merge(
                    parent::rules(),
                    $this->confirmationRules,
                    [
                        [[$this->createdByAttribute], 'safe'],
                        [$this->contentAttribute, 'required'],
                        [[$this->createdByAttribute], 'string', 'max' => 36],
                        [$this->contentAttribute, 'email'],
                        [[$this->createdByAttribute, $this->idAttribute], 'unique', 'targetAttribute' =>
                            [$this->createdByAttribute, $this->idAttribute],
                        ],
                    ]
            );
        }
        return $this->_blameableRules;
    }

    public function setBlameableRules($rules = [])
    {
        $this->_blameableRules = $rules;
    }

    public function getBlameableBehaviors()
    {
        if (empty($this->_blameableBehaviors) || !is_array($this->_blameableBehaviors))
        {
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

    public function setBlameableBehaviors($behaviors = [])
    {
        $this->_blameableBehaviors = $behaviors;
    }
    
    /**
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     */
    public function onContentChanged($event)
    {
        $sender = $event->sender;
        $contentAttribute = $sender->contentAttribute;
        if (is_array($contentAttribute))
        {
            foreach ($contentAttribute as $attribute)
            {
                if ($sender->isAttributeChanged($attribute))
                {
                    $sender->confirmation = self::$CONFIRM_FALSE;
                    break;
                }
            }
        } else {
            if ($sender->isAttributeChanged($contentAttribute))
                $sender->confirmation = self::$CONFIRM_FALSE;
        }
    }
    
    /**
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @return string the guid of current user.
     */
    public function onGetCurrentUserGuid($event)
    {
        $sender = $event->sender;
        $identity = \Yii::$app->user->identity;
        if (!is_string($sender->identityGuidAttribute))
        {
            $sender->identityGuidAttribute = $sender->createdByAttribute;
        }
        $identityGuidAttribute = $sender->identityGuidAttribute;
        return $identity->$identityGuidAttribute;
    }
}