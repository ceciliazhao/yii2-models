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

use vistart\Models\tests\data\ar\AdditionalAccount;
use vistart\Models\tests\data\ar\User;
/**
 * Description of BaseAdditionalAccountModelTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseAdditionalAccountModelTest extends TestCase {
    
    private function prepareUser() {
        $user = new User(['password' => '123456']);
        $aa = $user->createModel(AdditionalAccount::className());
        $user->register([$aa]);
        return $user;
    }
    
    public function testInit() {
        $user = new User(['password' => '123456']);
        $aa = $user->createModel(AdditionalAccount::className());
        $this->assertTrue($user->register([$aa]));
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
    
    /**
     * @depends testInit
     */
    public function testNonPassword() {
        $user = $this->prepareUser();
        $aa = AdditionalAccount::findOne(['user_guid' => $user->guid]);
        $this->assertFalse($aa->independentPassword);
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
    
    /**
     * @depends testNonPassword
     */
    public function testPassword() {
        $user = $this->prepareUser();
        $aa = AdditionalAccount::findOne(['user_guid' => $user->guid]);
        $aa->passwordHashAttribute = 'pass_hash';
        $aa->password = '123456';
        $this->assertTrue($aa->save());
        $passwordHashAttribute = $aa->passwordHashAttribute;
        $this->assertStringStartsWith('$2y$' . $aa->passwordCost . '$', $aa->$passwordHashAttribute);
        $this->assertTrue($aa->validatePassword('123456'));
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
    
    /**
     * @depends testPassword
     */
    public function testDisableLogin() {
        $user = $this->prepareUser();
        $aa = AdditionalAccount::findOne(['user_guid' => $user->guid]);
        $this->assertFalse($aa->enableLoginAttribute);
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
    
    public function testEnableLogin() {
        $user = $this->prepareUser();
        $aa = AdditionalAccount::findOne(['user_guid' => $user->guid]);
        $aa->enableLoginAttribute = 'enable_login';
        $this->assertFalse($aa->canBeLogon);
        $aa->canBeLogon = true;
        $this->assertTrue($aa->canBeLogon);
        $enableLoginAttribute = $aa->enableLoginAttribute;
        $this->assertEquals(1, $aa->$enableLoginAttribute);
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
}
