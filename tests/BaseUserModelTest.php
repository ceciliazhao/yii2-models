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
/**
 * Description of BaseUserModelTest
 *
 * @author i
 */
class BaseUserModelTest extends TestCase {
    //use ActiveRecordTestTrait;
    /*
      public function testPushAndPop()
      {
      $stack = [];
      $this->assertEquals(0, count($stack));

      array_push($stack, 'foo');
      $this->assertEquals('foo', $stack[count($stack) - 1]);
      $this->assertEquals(1, count($stack));

      $this->assertEquals('foo', array_pop($stack));
      $this->assertEquals(0, count($stack));
      }
     */

    public function testNewUser() {
        $user = new User();
        $statusAttribute = $user->statusAttribute;
        $this->assertEquals(1, $user->$statusAttribute);
    }

    public function setUp() {
        parent::setUp();
        User::$db = $this->getConnection();
    }
}
