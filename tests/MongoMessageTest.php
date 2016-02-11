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
     * @group mongo
     * @group message
     * @depends testNew
     */
    public function testRead()
    {
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
        $user = static::prepareUser();
        $other = static::prepareUser();
        $message = $user->create(MongoMessage::className(), ['content' => 'message', 'other_guid' => $other->guid]);
        $this->assertTrue($message->save());
        
        $this->assertEquals(0, MongoMessage::find()->byIdentity($user)->received()->count());
        $this->assertEquals(1, MongoMessage::find()->byIdentity($user)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->received()->count());
        $this->assertEquals(0, MongoMessage::find()->byIdentity($other)->unreceived()->count());
        
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->received()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($user->guid)->unreceived()->count());
        $this->assertEquals(0, MongoMessage::find()->recipients($other->guid)->received()->count());
        $this->assertEquals(1, MongoMessage::find()->recipients($other->guid)->unreceived()->count());
        $this->assertEquals(1, $message->delete());
        $this->assertTrue($user->deregister());
        $this->assertTrue($other->deregister());
    }
}
