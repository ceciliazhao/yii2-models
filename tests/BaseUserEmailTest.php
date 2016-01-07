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
use vistart\Helpers\Ip;
use Yii;

/**
 * Description of BaseUserEmailTest
 *
 * @author i
 */
class BaseUserEmailTest extends TestCase {

    public function setUp() {
        parent::setUp();
        UserEmail::$db = $this->getConnection();
    }
    
    public function testInit() {
        UserEmail::deleteAll();
    }
    
    /**
     * @depends testInit
     */
    public function testNew() {
        $email = new UserEmail();
        $this->assertNotNull($email);
        
        var_dump($email->rules());
    }
}
