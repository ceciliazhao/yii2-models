<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\tests;

use vistart\Models\tests\data\ar\UserEmail;
use vistart\Models\tests\data\ar\User;
use Yii;

/**
 * 
 * @author vistart <i@vistart.name>
 */
class BaseUserEmailTest extends TestCase {

    public function testInit() {
        //UserEmail::deleteAll();
    }

    /**
     * @depends testInit
     */
    public function testNew() {
        $email = new UserEmail();
        $this->assertNotNull($email);
        $user = new User();
        $email = $user->createModel(UserEmail::className(), ['email' => 'i@vistart.name', 'type' => UserEmail::TYPE_HOME]);
        $this->assertNotNull($email);
        $this->assertTrue($user->register([$email]));
        $user = User::findOne($user->guid);
        $this->assertInstanceOf(User::className(), $user);
        $this->assertInstanceOf(UserEmail::className(), $user->userEmails[0]);
        $guid = $user->guid;
        $this->assertTrue($user->deregister());
        $user = User::findOne($guid);
        $email = UserEmail::findOne(['user_guid' => $guid]);
        $this->assertNull($user);
        $this->assertNull($email);
    }

}
