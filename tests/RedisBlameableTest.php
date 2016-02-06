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
use vistart\Models\tests\data\ar\RedisBlameable;

/**
 * Description of RedisBlameableTest
 *
 * @author vistart <i@vistart.name>
 */
class RedisBlameableTest extends TestCase
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
     * @group redis
     * @group blameable
     */
    public function testNew()
    {
        $user = static::prepareUser();
        $content = (string)mt_rand(1, 65535);
        $blameable = $user->create(RedisBlameable::className(), ['content' => $content]);
        if ($blameable->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($blameable->errors);
            $this->fail();
        }
        $blameable = RedisBlameable::findByIdentity($user)->one();
        $this->assertInstanceOf(RedisBlameable::className(), $blameable);
        $this->assertEquals($content, $blameable->content);
        $this->assertEquals(1, $blameable->delete());
        $this->assertNull(RedisBlameable::findByIdentity($user)->one());
        $this->assertTrue($user->deregister());
    }
}
