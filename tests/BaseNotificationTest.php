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

use vistart\Models\tests\data\ar\User;
use vistart\Models\tests\data\ar\Notification;
use vistart\Models\tests\data\ar\MongoNotificationRead;

/**
 * Description of BaseNotificationTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseNotificationTest extends TestCase
{

    /**
     * 
     * @return User
     */
    private function prepareUser()
    {
        $user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        return $user;
    }

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
