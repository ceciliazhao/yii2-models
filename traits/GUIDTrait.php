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

use vistart\helpers\Number;
use yii\base\ModelEvent;

/**
 * Entity features concerning GUID.
 * @property-read array $guidRules
 * @property string $guid
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait GUIDTrait
{

    /**
     * @var string REQUIRED. The attribute that will receive the GUID value.
     */
    public $guidAttribute = 'guid';

    /**
     * Attach `onInitGuidAttribute` event.
     * @param string $eventName
     */
    protected function attachInitGuidEvent($eventName)
    {
        $this->on($eventName, [$this, 'onInitGuidAttribute']);
    }

    /**
     * Initialize the GUID attribute with new generated GUID.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param Event $event
     * @since 1.1
     */
    public function onInitGuidAttribute($event)
    {
        $sender = $event->sender;
        $guidAttribute = $sender->guidAttribute;
        if (is_string($guidAttribute)) {
            $sender->$guidAttribute = static::generateGuid();
        }
    }

    /**
     * Generate GUID. It will check if the generated GUID existed in database
     * table, if existed, it will regenerate one.
     * @return string the generated GUID.
     */
    public static function generateGuid()
    {
        return Number::guid();
    }

    /**
     * Check if the $uuid existed in current database table.
     * @param string $guid the GUID to be checked.
     * @return boolean Whether the $guid exists or not.
     */
    public static function checkGuidExists($guid)
    {
        return (self::findOne($guid) !== null);
    }

    /**
     * Get the rules associated with guid attribute.
     * @return array rules.
     */
    public function getGuidRules()
    {
        $rules = [];
        if (is_string($this->guidAttribute)) {
            $rules = [
                [[$this->guidAttribute], 'required',],
                [[$this->guidAttribute], 'unique',],
                [[$this->guidAttribute], 'string', 'max' => 36],
            ];
        }
        return $rules;
    }

    /**
     * Get guid, in spite of guid attribute name.
     * @return string
     */
    public function getGuid()
    {
        $guidAttribute = $this->guidAttribute;
        return is_string($guidAttribute) ? $this->$guidAttribute : null;
    }

    /**
     * Set guid, in spite of guid attribute name.
     * @param string $guid
     * @return string
     */
    public function setGuid($guid)
    {
        $guidAttribute = $this->guidAttribute;
        return is_string($guidAttribute) ? $this->$guidAttribute = $guid : null;
    }
}
