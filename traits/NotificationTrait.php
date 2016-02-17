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

    public function initNotificationEvents()
    {
        $this->on(static::EVENT_INIT, [$this, 'onRemoveExpired']);
    }
}