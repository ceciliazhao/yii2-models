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

namespace vistart\Models\tests\data\ar;

/**
 * Description of Notification
 *
 * @author vistart <i@vistart.name>
 */
class Notification extends \vistart\Models\models\BaseNotificationModel
{

    public static function tableName()
    {
        return '{{%user_notification}}';
    }
}
