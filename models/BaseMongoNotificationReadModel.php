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

use vistart\Models\traits\NotificationReadTrait;

/**
 * Description of BaseMongoNotificationLogModel
 *
 * @author vistart <i@vistart.name>
 */
abstract class BaseMongoNotificationReadModel extends BaseMongoBlameableModel
{
    use NotificationReadTrait;

    public $updatedAtAttribute = false;
    public $updatedByAttribute = false;
    public $enableIP = false;

}
