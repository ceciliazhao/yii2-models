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

use yii\helpers\Json;

/**
 * Description of NotificationRangeTrait
 *
 * @property array range
 * [
 *     'user' => [
 *     ],
 *     'status' => [
 *     ],
 *     'exclude' => false, (default)
 * ]
 * @author vistart <i@vistart.name>
 */
trait NotificationRangeTrait
{

    /**
     * @var string range attribute name.
     * We do not recommend you access this attribute directly.
     */
    public $rangeAttribute = 'range';

    public function getRange()
    {
        $rangeAttribute = $this->rangeAttribute;
        if (!is_string($rangeAttribute)) {
            return null;
        }
        try {
            $range = Json::decode($this->$rangeAttribute);
        } catch (\Exception $ex) {
            $range = [];
        }
        if (!isset($range['exclude']) || !$range['exclude']) {
            $range['exclude'] = false;
        } else {
            $range['exclude'] = true;
        }
        if (!isset($range['user'])) {
            $range['user'] = [];
        }
        if (!isset($range['status'])) {
            $range['status'] = [];
        }
        return $range;
    }

    public function setRange($range)
    {
        $rangeAttribute = $this->rangeAttribute;
        if (!is_string($rangeAttribute)) {
            return null;
        }
        if (!is_array($range)) {
            $range = [];
        }
        if (isset($range['exclude']) && $range['exclude'] == false) {
            unset($range['exclude']);
        }
        if (isset($range['user']) && empty($range['user'])) {
            unset($range['user']);
        }
        if (isset($range['status']) && empty($range['status'])) {
            unset($range['status']);
        }
        return $this->$rangeAttribute = Json::encode($range);
    }

    public function inRange($user, $status = null)
    {
        $rangeAttribute = $this->rangeAttribute;
        if (!is_string($rangeAttribute)) {
            return false;
        }
        $range = $this->getRange();
        if ($status === null) {
            return $range['exclude'] ? !in_array($user, $range['user']) : in_array($user, $range['user']);
        }
        return $range['exclude'] ? (!in_array($user, $range['user']) || !in_array($status, $range['status'])) : (in_array($user, $range['user']) || in_array($status, $range['status']));
    }

    public function getNotificationRangeRules()
    {
        return is_string($this->rangeAttribute) ? [
            [$this->rangeAttribute, 'string'],
            [$this->rangeAttribute, 'default', 'value' => '[]'],
            [$this->rangeAttribute, 'validateRange'],
            ] : [];
    }

    public function validateRange()
    {
        try {
            $rangeAttribute = $this->rangeAttribute;
            Json::decode($this->$rangeAttribute);
        } catch (\Exception $ex) {
            $this->addError($ex->getMessage());
            return false;
        }
        return true;
    }
}
