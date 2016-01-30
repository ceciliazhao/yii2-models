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
        // 此时不应该为 null
        $this->assertNotNull($email);
        
        $user = new User();
        $email = $user->findOneOrCreate(UserEmail::className(), ['email' => 'i@vistart.name', 'type' => UserEmail::TYPE_HOME]);
        // 此时不应该为 null
        $this->assertNotNull($email);
        // 与用户一同注册
        $this->assertTrue($user->register([$email]));
        //var_dump($email->rules());
        $user = User::findOne($user->guid);
        // 此处应为 User 实例。
        $this->assertInstanceOf(User::className(), $user);
        // 此处应为 UserEmail 实例。
        $this->assertInstanceOf(UserEmail::className(), $user->userEmails[0]);
        // 此时属于该用户的 email 应该只有一个。
        $this->assertEquals(1, $email->count());
        
        $email_guid = $user->userEmails[0]->guid;
        $email = $user->findOneOrCreate(UserEmail::className());
        $this->assertEquals($email_guid, $email->guid);
        $email = $user->findOneOrCreate(UserEmail::className(), [$email->guidAttribute, $email_guid]);
        $this->assertEquals($email_guid, $email->guid);
        
        $guid = $user->guid;
        // 此处应该注销成功。
        $this->assertTrue($user->deregister());
        $user = User::findOne($guid);
        $email = UserEmail::findOne(['user_guid' => $guid]);
        
        // 此时应该找不到 $user 和 $email。
        $this->assertNull($user);
        $this->assertNull($email);
        
    }

}
