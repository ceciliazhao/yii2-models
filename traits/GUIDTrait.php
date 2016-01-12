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

use vistart\Helpers\Number;

/**
 * Entity features concerning GUID.
 * @property-read array $guidRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait GUIDTrait {

    /**
     * @var string REQUIRED. The attribute that will receive the GUID value.
     */
    public $guidAttribute = 'guid';

    /**
     * Initialize the GUID attribute with new generated GUID.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitGuidAttribute($event) {
        $sender = $event->sender;
        $guidAttribute = $sender->guidAttribute;
        if (is_string($guidAttribute)) {
            $sender->$guidAttribute = self::GenerateGuid();
        }
    }

    /**
     * Generate GUID. It will check if the generated GUID existed in database
     * table, if existed, it will regenerate one.
     * @return string the generated GUID.
     */
    public static function GenerateGuid() {
        return Number::guid();
    }

    /**
     * Check if the $uuid existed in current database table.
     * @param string $uuid the GUID to be checked.
     * @return boolean Whether the $guid exists or not.
     */
    public static function CheckGuidExists($uuid) {
        return (self::findOne($uuid) !== null);
    }

    public function getGuidRules() {
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

}
