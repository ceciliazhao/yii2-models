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
 * This trait should be used in models extended from models used BlameableTrait.
 * Notice: The models used BlameableTrait are also models used EntityTrait.
 *
 * @author vistart <i@vistart.name>
 */
trait MessageTrait
{

    public $otherGuidAttribute = 'other_guid';
    public $attachmentAttribute = 'attachment';
    public $expiration = 604800; // 7 days
    public $receivedAtAttribute = 'received_at';
    public $readAtAttribute = 'read_at';
    public static $eventMessageReceived = 'messageReceived';
    public static $eventMessageRead = 'messageRead';
    public $permitChangeContent = false;
    public $permitChangeReceivedAt = false;

    public function hasBeenReceived()
    {
        return is_string($this->receivedAtAttribute) ? !$this->isInitDatetime($this->receivedAtAttribute) : false;
    }

    public function hasBeenRead()
    {
        return is_string($this->readAtAttribute) ? !$this->isInitDatetime($this->readAtAttribute) : false;
    }

    public function touchReceived()
    {
        return $this->setReceivedAt(static::currentDatetime());
    }

    public function touchRead()
    {
        return $this->setReadAt(static::currentDatetime());
    }

    public function getReceivedAt()
    {
        if (is_string($this->receivedAtAttribute)) {
            $raAttribute = $this->receivedAtAttribute;
            return $this->$raAttribute;
        }
        return null;
    }

    public function setReceivedAt($receivedAt)
    {
        if (is_string($this->receivedAtAttribute)) {
            $raAttribute = $this->receivedAtAttribute;
            return $this->$raAttribute = $receivedAt;
        }
        return null;
    }

    public function getReadAt()
    {
        if (is_string($this->readAtAttribute)) {
            $raAttribute = $this->readAtAttribute;
            return $this->$raAttribute;
        }
        return null;
    }

    public function setReadAt($readAt)
    {
        if (is_string($this->readAtAttribute)) {
            $raAttribute = $this->readAtAttribute;
            return $this->$raAttribute = $readAt;
        }
        return null;
    }

    /**
     * @param \yii\base\ModelEvent $event
     */
    public function onInitReceivedAtAttribute($event)
    {
        $sender = $event->sender;
        /* @var $sender static */
        $sender->setReceivedAt(static::getInitDatetime($event));
    }

    /**
     * @param \yii\base\ModelEvent $event
     */
    public function onInitReadAtAttribute($event)
    {
        $sender = $event->sender;
        /* @var $sender static */
        $sender->setReadAt(static::getInitDatetime($event));
    }

    /**
     * We consider you have received the message if you read it.
     * @param \yii\base\ModelEvent $event
     */
    public function onReadAtChanged($event)
    {
        $sender = $event->sender;
        if (!is_string($sender->readAtAttribute)) {
            return;
        }
        $raAttribute = $sender->readAtAttribute;
        $reaAttribute = $sender->receivedAtAttribute;
        if ($sender->$raAttribute != $sender->initDatetime() && $sender->$reaAttribute == $sender->initDatetime()) {
            $sender->$reaAttribute = $sender->currentDatetime();
        }
        $oldRa = $sender->getOldAttribute($raAttribute);
        if ($oldRa != $sender->initDatetime() && $sender->$raAttribute != $oldRa) {
            $sender->$raAttribute = $oldRa;
        }
    }

    /**
     * You are not allowed to change receive time if you have received it.
     * @param \yii\base\ModelEvent $event
     */
    public function onReceivedAtChanged($event)
    {
        $sender = $event->sender;
        if ($sender->permitChangeReceivedAt) {
            return;
        }
        $raAttribute = $sender->receivedAtAttribute;
        $oldRa = $sender->getOldAttribute($raAttribute);
        if ($oldRa != $sender->initDatetime() && $sender->$raAttribute != $oldRa) {
            $sender->$raAttribute = $oldRa;
        }
    }

    /**
     * You are not allowed to change the content if it is not new message.
     * @param \yii\base\ModelEvent $event
     */
    public function onContentChanged($event)
    {
        $sender = $event->sender;
        if ($sender->permitChangeContent) {
            return;
        }
        $cAttribute = $sender->contentAttribute;
        $oldContent = $sender->getOldAttribute($cAttribute);
        if ($oldContent != $sender->$cAttribute) {
            $sender->$cAttribute = $oldContent;
        }
    }

    public function initMessageEvents()
    {
        $this->on(static::EVENT_BEFORE_INSERT, [$this, 'onInitReceivedAtAttribute']);
        $this->on(static::EVENT_BEFORE_INSERT, [$this, 'onInitReadAtAttribute']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'onReadAtChanged']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'onReceivedAtChanged']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'onContentChanged']);
    }

    public function getMessageRules()
    {
        $rules = [
            [$this->otherGuidAttribute, 'required'],
            [$this->otherGuidAttribute, 'string', 'max' => 36],
        ];
        if (is_string($this->attachmentAttribute)) {
            $rules[] = [$this->attachmentAttribute, 'safe'];
        }
        if (is_string($this->receivedAtAttribute)) {
            $rules[] = [$this->receivedAtAttribute, 'safe'];
        }
        if (is_string($this->readAtAttribute)) {
            $rules[] = [$this->readAtAttribute, 'safe'];
        }
        return $rules;
    }

    public function rules()
    {
        return array_merge(parent::rules(), $this->getMessageRules());
    }

    public function enabledFields()
    {
        $fields = parent::enabledFields();
        $fields[] = $this->otherGuidAttribute;
        if (is_string($this->attachmentAttribute)) {
            $fields[] = $this->attachmentAttribute;
        }
        if (is_string($this->receivedAtAttribute)) {
            $fields[] = $this->receivedAtAttribute;
        }
        if (is_string($this->readAtAttribute)) {
            $fields[] = $this->readAtAttribute;
        }
        return $fields;
    }
}