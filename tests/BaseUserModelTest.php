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

use vistart\Models\tests\data\ar\User;
use vistart\Helpers\Ip;
use Yii;

/**
 * Description of BaseUserModelTest
 *
 * @author vistart <i@vistart.name>
 * @since 2.0
 */
class BaseUserModelTest extends TestCase {

    public function setUp() {
        parent::setUp();
        User::$db = $this->getConnection();
    }

    public function testNewUser() {
        $user = new User();
        $this->assertNotEmpty($user);
        $statusAttribute = $user->statusAttribute;
        $this->assertEquals(1, $user->$statusAttribute);
        $password = '123456';
        $user->password = $password;
        $passwordHashAttribute = $user->passwordHashAttribute;
        $this->assertEquals(true, $this->validatePassword($password, $user->$passwordHashAttribute));
        $this->assertEquals(false, $this->validatePassword('1234567', $user->$passwordHashAttribute));
    }

    public function testGUID() {
        $user = new User();
        $this->assertNotEmpty($user->guid);
        
        $user = new User();
        $guidAttribute = $user->guidAttribute;
        $this->assertEquals($user->guid, $user->$guidAttribute);
    }

    public function testID() {
        $user = new User();
        $this->assertEmpty($user->id);

        $user = new User(['idAttribute' => 'id']);
        $this->assertNotEmpty($user->id);
        $idAttribute = $user->idAttribute;
        $this->assertEquals($user->id, $user->$idAttribute);
    }
    
    public function testIP() {
        $ipAddress = '::1';
        $user = new User(['ipAddress' => $ipAddress]);
        $this->assertEquals(true, $user->enableIP);
        $this->assertEquals($ipAddress, $user->ipAddress);
        $ipTypeAttribute = $user->ipTypeAttribute;
        $this->assertEquals(Ip::IPv6, $user->$ipTypeAttribute);
    }

    public function testPassword() {
        $password = '123456';
        $user = new User();
        $this->assertEquals(false, $user->hasEventHandlers(User::$EVENT_AFTER_SET_PASSWORD));
        $this->assertEquals(false, $user->hasEventHandlers(User::$EVENT_BEFORE_VALIDATE_PASSWORD));
        
        $user->on(User::$EVENT_AFTER_SET_PASSWORD, function($event){
            $this->assertTrue(true, 'EVENT_AFTER_SET_PASSWORD');
            $sender = $event->sender;
            $this->assertInstanceOf(User::className(), $sender);
        });
        $this->assertEquals(true, $user->hasEventHandlers(User::$EVENT_AFTER_SET_PASSWORD));
        
        $user->on(User::$EVENT_BEFORE_VALIDATE_PASSWORD, function($event){
            $this->assertTrue(true, 'EVENT_BEFORE_VALIDATE_PASSWORD');
            $sender = $event->sender;
            $this->assertInstanceOf(User::className(), $sender);
        });
        $this->assertEquals(true, $user->hasEventHandlers(User::$EVENT_BEFORE_VALIDATE_PASSWORD));
        
        $user->password = $password;
        $passwordHashAttribute = $user->passwordHashAttribute;
        $this->assertEquals(true, $this->validatePassword($password, $user->$passwordHashAttribute));
        $this->assertEquals(false, $this->validatePassword($password . ' ', $user->$passwordHashAttribute));
    }

    public function testStatus() {
        $user = new User();
        $statusAttribute = $user->statusAttribute;
        $this->assertEquals(1, $user->$statusAttribute);
    }
    
    public function testTimestamp() {
        $user = new User();
        $createdAtAttribute = $user->createdAtAttribute;
        $updatedAtAttribute = $user->updatedAtAttribute;
        $this->assertNull($user->$createdAtAttribute);
        $this->assertNull($user->$updatedAtAttribute);
        $result = $user->register();
        if ($result instanceof \yii\db\Exception){
            var_dump($result->getMessage());
            $this->assertFalse(false);
        } else {
            $this->assertTrue($result);
        }
        
        $this->assertNotNull($user->$createdAtAttribute);
        $this->assertNotNull($user->$updatedAtAttribute);
        $this->assertTrue($user->deregister());
    }
    
    public function testRegister() {
        $user = new User();
        $this->assertTrue($user->register());
        $this->assertTrue($user->deregister());
    }

    private function validatePassword($password, $hash) {
        return Yii::$app->security->validatePassword($password, $hash);
    }

}
