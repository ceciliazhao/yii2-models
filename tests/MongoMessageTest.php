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
        //var_dump($message->rules());
        //var_dump($message->behaviors());
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
}
