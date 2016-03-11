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

use vistart\Models\tests\data\ar\MongoNotification;
use vistart\Models\tests\data\ar\MongoNotificationRead;

/**
 * Description of MongoNotificationTest
 *
 * @author vistart <i@vistart.name>
 */
class MongoNotificationTest extends MongoTestCase
{

    /**
     * @group mongo
     * @group notification
     */
    public function testNew()
    {
        $user = static::prepareUser();
        $notification = $user->create(MongoNotification::className(), ['content' => 'Notification']);
        /* @var $notification MongoNotification */
        $this->assertTrue($notification->save());
        $this->assertTrue(MongoNotificationRead::read($user, $notification));
        $log = MongoNotificationRead::findByIdentity($user)->content($notification->guid)->one();
        $this->assertInstanceOf(MongoNotificationRead::className(), $log);
        $this->assertEquals(1, $log->delete());

        $this->assertEquals(1, $notification->delete());
        $this->assertTrue($user->deregister());
    }

    /**
     * @group mongo
     * @group notification
     * @depends testNew
     */
    public function testRead()
    {
        $user = static::prepareUser();
        $notification = $user->create(MongoNotification::className(), ['content' => 'Notification']);
        $this->assertTrue($notification->save());
        $this->assertTrue(MongoNotificationRead::read($user, $notification));
        $this->assertEquals(1, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());
        $this->assertTrue(MongoNotificationRead::read($user, $notification));
        $this->assertEquals(1, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());

        $this->assertEquals(0, MongoNotificationRead::vacuum($user, MongoNotification::className()));

        $this->assertTrue(MongoNotificationRead::unread($user, $notification));
        $this->assertEquals(0, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());
        $this->assertTrue(MongoNotificationRead::unread($user, $notification));
        $this->assertEquals(0, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());

        $this->assertEquals(1, $notification->delete());
        $this->assertFalse(MongoNotificationRead::read($user, $notification));
        $this->assertTrue(MongoNotificationRead::unread($user, $notification));

        $this->assertEquals(0, MongoNotificationRead::vacuum($user, MongoNotification::className()));
        $this->assertTrue($user->deregister());
    }

    /**
     * @group mongo
     * @group notification
     * @depends testRead
     */
    public function testVacuum()
    {
        $user = static::prepareUser();
        $notification = $user->create(MongoNotification::className(), ['content' => 'Notification']);
        $this->assertTrue($notification->save());
        $this->assertTrue(MongoNotificationRead::read($user, $notification));
        $this->assertEquals(1, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());
        $this->assertTrue(MongoNotificationRead::read($user, $notification));
        $this->assertEquals(1, (int) MongoNotificationRead::findByIdentity($user)->content($notification->guid)->count());

        $this->assertEquals(0, MongoNotificationRead::vacuum($user, MongoNotification::className()));

        $this->assertEquals(1, $notification->delete());

        $this->assertEquals(0, MongoNotificationRead::vacuum($user, MongoNotification::className()));
        $this->assertTrue($user->deregister());
    }
}
