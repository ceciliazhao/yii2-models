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

use vistart\Models\tests\data\ar\MultipleDomainsManager;
use Yii;

/**
 * 
 * @author vistart <i@vistart.name>
 */
class MultipleDomainsManagerTest extends TestCase {

    public function testInit() {
        //UserEmail::deleteAll();
    }

    /**
     * @depends testInit
     */
    public function testNew() {
        $MultipleDomainsManager = new MultipleDomainsManager();
        $urlManager = $MultipleDomainsManager->current;
        $myUrlManager = $MultipleDomainsManager->get('my');
        $loginUrlManager = $MultipleDomainsManager->get('login');
        $this->assertEquals('/site/index.html', $urlManager->createUrl('/site/index'));
        $this->assertEquals('/posts.html', $myUrlManager->createUrl('/post/index'));
        $this->assertEquals('/', $loginUrlManager->createUrl('/site/login'));
        $this->assertEquals('/logout', $loginUrlManager->createUrl('/site/logout'));
    }

}
