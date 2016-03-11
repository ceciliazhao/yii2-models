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

use yii\base\ModelEvent;

/**
 * This trait is used for building notification model.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait NotificationTrait
{
    use NotificationRangeTrait;

    public $notificationReadClass;
    public $linkAttribute = false;

    public function getLink()
    {
        $linkAttribute = $this->linkAttribute;
        if (is_string($linkAttribute)) {
            return $this->$linkAttribute;
        }
        return null;
    }

    public function setLink($link)
    {
        $linkAttribute = $this->linkAttribute;
        if (is_string($linkAttribute)) {
            return $this->$linkAttribute = $link;
        }
    }

    public function getNotificationRules()
    {
        $rules = $this->getNotificationRangeRules();

        if (is_string($this->linkAttribute)) {
            $rules[] = [
                $this->linkAttribute, 'string',
            ];
        }
        return $rules;
    }

    public function enabledFields()
    {
        $fields = parent::enabledFields();
        if (is_string($this->linkAttribute)) {
            $fields[] = $this->linkAttribute;
        }
        if (is_string($this->rangeAttribute)) {
            $fields[] = $this->rangeAttribute;
        }
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->getNotificationRules());
    }

    /**
     * 
     * @param ModelEvent $event
     * @return integer|false
     */
    public function onDeleteNotificationRead($event)
    {
        $sender = $event->sender;
        /* @var $sender static */
        if (!is_string($sender->notificationReadClass)) {
            return false;
        }
        $nrClass = $sender->notificationReadClass;
        $nrModels = $nrClass::find()->content($sender->guid)->all();
        $count = 0;
        foreach ($nrModels as $model) {
            $count += $model->delete();
        }
        return $count;
    }
}
