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
        $this->assertEquals(1, $aa->count());
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
    
    public function testRules() {
        $user = $this->prepareUser();
        $aa = AdditionalAccount::findOne(['user_guid' => $user->guid]);
        $this->validateRules($aa->rules());
        $this->assertTrue($user->deregister());
        echo __METHOD__ . ":Done!\n";
    }
    
    private function AdditionalAccountRules() {
        return [
            [['guid'], 'required'],
            [['guid'], 'unique'],
            [['guid'], 'string', 'max' => 36],
        ];
    }
    
    private function validateRules($rules) {
        foreach ($rules as $key => $rule) {
            $this->assertTrue(is_array($rule));
            if (is_array($rule[0])) {
            } elseif (is_string($rule[0])) {
            } else {
                // 只有可能是字符串或数组，不可能为其他类型。
                $this->assertTrue(false);
            }
            //var_dump($rule);
        }
    }
}
