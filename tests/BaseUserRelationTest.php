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
use vistart\Models\tests\data\ar\UserRelation;
/**
 * Description of UserRelationTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationTest extends TestCase{
    private function prepareModels() {
        $user = new User(['password' => '123456']);
        $other_user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        $this->assertTrue($other_user->register());
        $ua = $user->createModel(UserRelation::className());
        $otherGuidAttribute = $ua->otherGuidAttribute;
        $ua->$otherGuidAttribute = $other_user->guid;
        if ($ua->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($ua->errors);
        }
        return ['users' => [$user, $other_user], $ua];
    }
    
    private function destroyModels($users = []) {
        foreach ($users as $user) {
            $this->assertTrue($user->deregister());
        }
    }
    
    public function testNew() {
        $models = $this->prepareModels();
        $this->destroyModels($models['users']);
        echo __METHOD__ . ":Done!\n";
    }
    
    public function testDeregisterOne() {
        
    }
}
