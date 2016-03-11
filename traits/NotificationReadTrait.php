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

use vistart\Models\models\BaseMongoNotificationModel;
use vistart\Models\models\BaseNotificationModel;
use vistart\Models\models\BaseUserModel;

/**
 * Description of NotificationLog
 *
 * @author vistart <i@vistart.name>
 */
trait NotificationReadTrait
{

    /**
     * 
     * @param BaseUserModel $user
     * @param BaseMongoNotificationModel|BaseNotificationModel $notification
     * @return boolean False if notification was marked as read or it didn't exist.
     */
    public static function read($user, $notification)
    {
        if (empty($notification) || !$notification->findByIdentity($user)->guid($notification->guid)->one()) {
            return false;
        }
        $log = static::findByIdentity($user)->content($notification->guid)->one();
        if (!$log) {
            $log = $user->create(static::className(), ['content' => $notification->guid]);
        }
        return $log->save();
    }

    /**
     * 
     * @param BaseUserModel $user
     * @param BaseMongoNotificationModel|BaseNotificationModel $notification
     * @return boolean True if notification was marked as unread or it didn't exist.
     */
    public static function unread($user, $notification)
    {
        if (empty($notification) || !$notification->findByIdentity($user)->guid($notification->guid)->one()) {
            return true;
        }
        $log = static::findByIdentity($user)->content($notification->guid)->one();
        if ($log) {
            return $log->delete() == 1;
        }
        return true;
    }

    /**
     * Vacuum all the invalid notification read records.
     * @param BaseUserModel $user
     * @param string $notificationClass
     * @return integer The sum of notification read records vacuumed.
     */
    public static function vacuum($user, $notificationClass)
    {
        $logs = static::findByIdentity($user)->all();
        $count = 0;
        foreach ($logs as $log) {
            if (is_string($notificationClass) && ((int) ($notificationClass::findByIdentity($user)->guid($log->content)->count())) > 0) {
                continue;
            }
            $count += $log->delete();
        }
        return $count;
    }
}
