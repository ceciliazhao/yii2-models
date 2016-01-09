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
        $urlManager = $MultipleDomainsManager->get('my');
        $this->assertEquals('/site/index.html', $urlManager->createUrl('/site/index'));
        $this->assertEquals('/login', $urlManager->createUrl('/site/login'));
        var_dump($urlManager->createAbsoluteUrl('/site/index'));
        var_dump($urlManager->createAbsoluteUrl('/site/login'));
    }

}
