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

namespace vistart\Models\tests;

use vistart\Models\tests\data\ar\Notification;

/**
 * Description of BaseNotificationTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseNotificationTest extends MongoTestCase
{

    /**
     * @group notification
     */
    public function testNew()
    {
        $user = $this->prepareUser();
        $notification = $user->create(Notification::className(), ['content' => 'Notification']);
        $this->assertTrue($notification->save());
        $this->assertEquals(1, $notification->delete());
        $this->assertTrue($user->deregister());
    }
}
