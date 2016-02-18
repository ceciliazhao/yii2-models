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
 * Description of NotificationTrait
 *
 * @author vistart <i@vistart.name>
 */
trait NotificationTrait
{

    public $rangeAttribute = 'range';
    public $linkAttrbute = '';

    public function getRange()
    {
        $rangeAttribute = $this->rangeAttribute;
        return $this->$rangeAttribute;
    }

    public function setRange($range)
    {
        $rangeAttribute = $this->rangeAttribute;
        return $this->$rangeAttribute = $range;
    }

    public function getLink()
    {
        $linkAttribute = $this->linkAttrbute;
        return $this->$linkAttribute;
    }

    public function setLink($link)
    {
        $linkAttribute = $this->linkAttrbute;
        return $this->$linkAttribute = $link;
    }

    public function getNotificationRules()
    {
        $rules = [];
        if (is_string($this->rangeAttribute)) {
            $rules[] = [
                $this->rangeAttribute, 'string',
            ];
        }
        if (is_string($this->linkAttrbute)) {
            $rules[] = [
                $this->linkAttrbute, 'string',
            ];
        }
        return $rules;
    }
}
