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

use vistart\Models\tests\data\ar\MongoMessage;

/**
 * Description of MongoMessageTest
 *
 * @author vistart <i@vistart.name>
 */
class MongoMessageTest extends MongoTestCase
{

    /**
     * @group mongo
     * @group message
     * @group init
     */
    public function testInit()
    {
        $time = null;
        $offset = (int) 0;
        $this->assertTrue(date('Y-m-d H:i:s', strtotime(((int) $offset >= 0 ? "+$offset" : "-" . abs($offset)) . " seconds", is_string($time) ? strtotime($time) : time())) > null);
    }

    /**
     * @group mongo
     * @group message
     * @depends testInit
     */
    public function testNew()
    {
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
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
     * @group mongo
     * @group message
     * @depends testNew
     */
    public function testExpired()
    {
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        if ($message->isExpired) {
            echo "time format: ";
            var_dump($message->timeFormat);
            echo "created at: ";
            var_dump($message->createdAt);
            echo "expired at: ";
            var_dump($message->expiredAt);
            $this->fail();
        } else {
            $this->assertTrue(true);
        }
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }

    /**
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onReceived($event)
    {
        //echo "Received Event Triggered\n";
        return $this->isReceived = true;
    }

    /**
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onRead($event)
    {
        //echo "Read Event Triggered\n";
        return $this->isRead = true;
    }

    public function onShouldNotBeExpiredRemoved($event)
    {
        $sender = $event->sender;
        var_dump($sender->offsetDatetime(-$sender->expiredAt));
        var_dump($sender->createdAt);
        $this->fail("The message model has been removed if you meet this message.\n"
            . "This event should not be triggered.");
    }

    public $isRead = false;
    public $isReceived = false;

    /**
     * @group mongo
     * @group message
     * @depends testExpired
     */
    public function testRead()
    {
        $this->isRead = false;
        $this->isReceived = false;
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        $message_id = $message->guid;
        $this->assertEquals(0, MongoMessage::find()->byIdentity($user)->read()->count());
        $this->assertEquals(1, MongoMessage::find()->byIdentity($user)->unread()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->read()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->unread()->count());

        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->read()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->unread()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($other->guid)->read()->count());
        $this->assertEquals(1, MongoMessage::find()->recipients($other->guid)->unread()->count());

        $message = MongoMessage::find()->byIdentity($user)->one();
        $message1 = MongoMessage::find()->guid($message_id)->one();
        $this->assertEquals($message->guid, $message1->guid);
        $message->on(MongoMessage::$eventMessageReceived, [$this, 'onReceived']);
        $message->on(MongoMessage::$eventMessageRead, [$this, 'onRead']);
        $message->on(MongoMessage::$eventExpiredRemoved, [$this, 'onShouldNotBeExpiredRemoved']);
        $this->assertInstanceOf(MongoMessage::className(), $message);
        $message->content = 'new message';
        $this->assertTrue($message->save());
        $this->assertFalse($this->isReceived);
        $this->assertFalse($this->isRead);
        $this->assertEquals('message', $message->content);
        if ($message->hasBeenRead()) {
            var_dump(MongoMessage::$initDatetime);
            var_dump($message->readAt);
            var_dump(MongoMessage::$initDatetime == $message->readAt);
            $this->fail();
        } else {
            $this->assertTrue(true);
        }
        $this->assertFalse($message->hasBeenReceived());
        if ($message->touchRead() && $message->save()) {
            $this->assertTrue(true);
            $this->assertTrue($message->hasBeenReceived());
            $this->assertTrue($message->hasBeenRead());
            if ($this->isReceived) {
                $this->assertTrue(true);
            } else {
                var_dump($message->isAttributeChanged($message->receivedAtAttribute));
                $this->fail();
            }
            if ($this->isRead) {
                $this->assertTrue(true);
            } else {
                var_dump($message->isAttributeChanged($message->readAtAttribute));
                $this->fail();
            }
        } else {
            var_dump($message->errors);
            $this->fail();
        }

        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }

    /**
     * @group mongo
     * @group message
     * @depends testRead
     */
    public function testReceived()
    {
        $this->isRead = false;
        $this->isReceived = false;
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        $message_id = $message->guid;
        $this->assertEquals(0, MongoMessage::findByIdentity($user)->received()->count());
        $this->assertEquals(1, MongoMessage::findByIdentity($user)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::findByIdentity($other)->received()->count());
        $this->assertEquals(0, MongoMessage::findByIdentity($other)->unreceived()->count());

        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->received()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($other->guid)->received()->count());
        $this->assertEquals(1, MongoMessage::find()->recipients($other->guid)->unreceived()->count());

        $message = MongoMessage::find()->recipients($other->guid)->one();
        $message1 = MongoMessage::find()->guid($message_id)->one();
        $this->assertEquals($message->guid, $message1->guid);
        $message->on(MongoMessage::$eventMessageReceived, [$this, 'onReceived']);
        $message->on(MongoMessage::$eventMessageRead, [$this, 'onRead']);
        $message->on(MongoMessage::$eventExpiredRemoved, [$this, 'onShouldNotBeExpiredRemoved']);
        $this->assertInstanceOf(MongoMessage::className(), $message);

        $this->assertFalse($message->hasBeenReceived());
        $this->assertFalse($message->hasBeenRead());
        if ($message->touchReceived() && $message->save()) {
            $this->assertTrue(true);
            $this->assertTrue($message->hasBeenReceived());
            $this->assertFalse($message->hasBeenRead());
            if ($this->isReceived) {
                $this->assertTrue(true);
            } else {
                var_dump($message->isAttributeChanged($message->receivedAtAttribute));
                $this->fail();
            }
            $this->assertFalse($this->isRead);
        } else {
            var_dump($message->errors);
            $this->fail();
        }
        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }
}
