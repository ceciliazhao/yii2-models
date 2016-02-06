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
use vistart\Models\tests\data\ar\MongoBlameable;

/**
 * Description of MongoBlameableTest
 *
 * @author vistart <i@vistart.name>
 */
class MongoBlameableTest extends MongoTestCase
{
    private static function prepareUser()
    {
        $user = new User(['password' => '123456']);
        if (!$user->register()) {
            $this->fail();
        }
        return $user;
    }

    /**
     * @group mongo
     * @group blameable
     */
    public function testNew()
    {
        $user = static::prepareUser();
        $content = (string) mt_rand(1, 65535);
        $blameable = $user->create(MongoBlameable::className(), ['content' => $content]);
        if ($blameable->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($blameable->errors);
            $this->fail();
        }
        
        $blameable = MongoBlameable::find()->id($blameable->id)->one();
        $this->assertInstanceOf(MongoBlameable::className(), $blameable);
        $this->assertEquals($content, $blameable->content);
        $cbAttribute = $blameable->createdByAttribute;
        $this->assertEquals($user->guid, $blameable->$cbAttribute);
        
        $blameable = MongoBlameable::findByIdentity($user)->one();
        $this->assertInstanceOf(MongoBlameable::className(), $blameable);
        $this->assertEquals($content, $blameable->content);
        
        $id = $blameable->id;
        $this->assertEquals(1, $blameable->delete());
        $this->assertNull(MongoBlameable::find()->id($id)->one());
        $this->assertTrue($user->deregister());
    }
}
