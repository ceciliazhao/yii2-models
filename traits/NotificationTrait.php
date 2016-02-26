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
 * This trait is used for building notification model.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait NotificationTrait
{
    use NotificationRangeTrait;

    public $logModel;
    public $linkAttrbute = '';

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
        $rules = $this->getNotificationRangeRules();

        if (is_string($this->linkAttrbute)) {
            $rules[] = [
                $this->linkAttrbute, 'string',
            ];
        }
        return $rules;
    }
}
