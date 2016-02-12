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

use vistart\Models\tests\data\ar\RedisMessage;

/**
 * Description of RedisMessageTest
 *
 * @author vistart <i@vistart.name>
 */
class RedisMessageTest extends TestCase
{

    /**
     * @group redis
     * @group message
     */
    public function testNew()
    {
        $user = RedisBlameableTest::prepareUser();
        $other = RedisBlameableTest::prepareUser();
        $message = $user->create(RedisMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        if ($message->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($message->errors);
            $this->fail();
        }
        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }

    /**
     * @group redis
     * @group message
     * @depends testNew
     */
    public function testRead()
    {
        $user = RedisBlameableTest::prepareUser();
        $other = RedisBlameableTest::prepareUser();
        $message = $user->create(RedisMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        $this->assertEquals(0, RedisMessage::find()->byIdentity($user)->read()->count());
        $this->assertEquals(1, RedisMessage::find()->byIdentity($user)->unread()->count());
        $this->assertEquals(0, RedisMessage::find()->byIdentity($other)->read()->count());
        $this->assertEquals(0, RedisMessage::find()->byIdentity($other)->unread()->count());

        $this->assertEquals(0, RedisMessage::find()->recipients($user->guid)->read()->count());
        $this->assertEquals(0, RedisMessage::find()->recipients($user->guid)->unread()->count());
        $this->assertEquals(0, RedisMessage::find()->recipients($other->guid)->read()->count());
        $this->assertEquals(1, RedisMessage::find()->recipients($other->guid)->unread()->count());

        $message = RedisMessage::find()->byIdentity($user)->one();
        $this->assertInstanceOf(RedisMessage::className(), $message);
        $message->content = 'new message';
        $this->assertTrue($message->save());
        $this->assertEquals('message', $message->content);
        if ($message->hasBeenRead()) {
            var_dump(RedisMessage::$initDatetime);
            var_dump($message->readAt);
            var_dump(RedisMessage::$initDatetime == $message->readAt);
            $this->fail();
        } else {
            $this->assertTrue(true);
        }
        $this->assertFalse($message->hasBeenReceived());
        if ($message->touchRead() && $message->save()) {
            $this->assertTrue(true);
            $this->assertTrue($message->hasBeenRead());
            $this->assertTrue($message->hasBeenReceived());
        } else {
            var_dump($message->errors);
            $this->fail();
        }

        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }

    /**
     * @group redis
     * @group message
     * @depends testRead
     */
    public function testReceived()
    {
        $user = RedisBlameableTest::prepareUser();
        $other = RedisBlameableTest::prepareUser();
        $message = $user->create(RedisMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        $this->assertEquals(0, RedisMessage::find()->byIdentity($user)->received()->count());
        $this->assertEquals(1, RedisMessage::find()->byIdentity($user)->unreceived()->count());
        $this->assertEquals(0, RedisMessage::find()->byIdentity($other)->received()->count());
        $this->assertEquals(0, RedisMessage::find()->byIdentity($other)->unreceived()->count());

        $this->assertEquals(0, RedisMessage::find()->recipients($user->guid)->received()->count());
        $this->assertEquals(0, RedisMessage::find()->recipients($user->guid)->unreceived()->count());
        $this->assertEquals(0, RedisMessage::find()->recipients($other->guid)->received()->count());
        $this->assertEquals(1, RedisMessage::find()->recipients($other->guid)->unreceived()->count());

        $message = RedisMessage::find()->recipients($other->guid)->one();
        $this->assertInstanceOf(RedisMessage::className(), $message);

        $this->assertFalse($message->hasBeenRead());
        $this->assertFalse($message->hasBeenReceived());
        if ($message->touchReceived() && $message->save()) {
            $this->assertTrue(true);
            $this->assertTrue($message->hasBeenReceived());
            $this->assertFalse($message->hasBeenRead());
        } else {
            var_dump($message->errors);
            $this->fail();
        }
        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }
}
