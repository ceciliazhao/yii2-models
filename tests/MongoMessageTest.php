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
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onReceived($event)
    {
        return $this->isReceived = true;
    }

    /**
     * 
     * @param \yii\base\ModelEvent $event
     */
    public function onRead($event)
    {
        return $this->isRead = true;
    }
    
    public $isRead = false;
    public $isReceived = false;

    /**
     * @group mongo
     * @group message
     * @depends testNew
     */
    public function testRead()
    {
        $this->isRead = false;
        $this->isReceived = false;
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());

        $this->assertEquals(0, MongoMessage::find()->byIdentity($user)->read()->count());
        $this->assertEquals(1, MongoMessage::find()->byIdentity($user)->unread()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->read()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->unread()->count());

        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->read()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->unread()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($other->guid)->read()->count());
        $this->assertEquals(1, MongoMessage::find()->recipients($other->guid)->unread()->count());

        $message = MongoMessage::find()->byIdentity($user)->one();
        $message->on(MongoMessage::$eventMessageReceived, [$this, 'onReceived']);
        $message->on(MongoMessage::$eventMessageRead, [$this, 'onRead']);
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
            $this->assertTrue($this->isReceived);
            $this->assertTrue($this->isRead);
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

        $this->assertEquals(0, MongoMessage::findByIdentity($user)->received()->count());
        $this->assertEquals(1, MongoMessage::findByIdentity($user)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::findByIdentity($other)->received()->count());
        $this->assertEquals(0, MongoMessage::findByIdentity($other)->unreceived()->count());

        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->received()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($other->guid)->received()->count());
        $this->assertEquals(1, MongoMessage::find()->recipients($other->guid)->unreceived()->count());

        $message = MongoMessage::find()->recipients($other->guid)->one();
        $message->on(MongoMessage::$eventMessageReceived, [$this, 'onReceived']);
        $message->on(MongoMessage::$eventMessageRead, [$this, 'onRead']);
        $this->assertInstanceOf(MongoMessage::className(), $message);

        $this->assertFalse($message->hasBeenReceived());
        $this->assertFalse($message->hasBeenRead());
        if ($message->touchReceived() && $message->save()) {
            $this->assertTrue(true);
            $this->assertTrue($message->hasBeenReceived());
            $this->assertFalse($message->hasBeenRead());
            $this->assertTrue($this->isReceived);
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
