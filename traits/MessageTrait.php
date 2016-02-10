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

    public $recipientGuidAttribute = 'other_guid';
    public $attachmentAttribute = 'attachment';
    public $expiration = 604800; // 7 days
    public $receivedAtAttribute = 'received_at';
    public $readAtAttribute = 'read_at';
    public static $eventMessageReceived = 'messageReceived';
    public static $eventMessageRead = 'messageRead';

    public function hasBeenReceived()
    {
        return is_string($this->receivedAtAttribute) ? $this->isInitDatetime($this->receivedAtAttribute) : false;
    }

    public function hasBeenRead()
    {
        return is_string($this->readAtAttribute) ? $this->isInitDatetim($this->readAtAttribute) : false;
    }

    public function touchReceived($event)
    {
        $this->setReceivedAt(static::getCurrentDatetime($event));
    }

    public function touchRead($event)
    {
        $this->setReadAt(static::getCurrentDatetime($event));
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

    public function getMessageRules()
    {
        $rules = [
            [$this->recipientGuidAttribute, 'required'],
            [$this->recipientGuidAttribute, 'string', 'max' => 36],
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
        $fields[] = $this->recipientGuidAttribute;
        if (is_string($this->attachmentAttribute)) {
            $fields[] = $this->attachmentAttribute;
        }
        if (is_string($this->receivedAtAttribute)) {
            $fields[] = $this->receivedAtAttribute;
        }
        if (is_string($this->readAtAttribute)) {
            $fields[] = $this->readAtAttribute;
        }
    }
}
