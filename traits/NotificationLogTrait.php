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
 * Description of NotificationLog
 *
 * @author vistart <i@vistart.name>
 */
trait NotificationLogTrait
{

    public static function read($user, $notification)
    {
        $log = static::find()->byIdentity($user)->id($notification->guid)->one();
        if (!$log) {
            $log = $user->create(static::className(), ['id' => $notification->guid]);
        }
        return $log->save();
    }

    public static function unread($user, $notification)
    {
        $log = static::find()->byIdentity($user)->id($notification - guid)->one();
        if ($log) {
            return $log->delete();
        }
        return true;
    }
}
