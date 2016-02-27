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

namespace vistart\Models\models;

use vistart\Models\traits\NotificationTrait;

/**
 * Description of BaseRedisNotificationModel
 *
 * @author vistart <i@vistart.name>
 */
abstract class BaseRedisNotificationModel extends BaseRedisBlameableModel
{
    use NotificationTrait;

    public $expiredAt = 604800;
    public $updatedAtAttribute = false;
    public $enableIP = false;
}
